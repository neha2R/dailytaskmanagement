<?php

use App\Models\Attendance;
use App\Models\Categories;
use App\Models\Companie;
use App\Models\Department;
use App\Models\Depo;
use App\Models\Division;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\InventoryType;
use App\Models\Position;
use App\Models\ProductMaster;
use App\Models\ProjectManagementType;
use App\Models\Role;
use App\Models\SubDivision;
use App\Models\TripExpense;
use App\Models\Uom;
use App\Models\User;
use App\Models\ManageTask;
use App\Models\VehicleManufacturer;
use App\Models\VehicleModel;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\WhDpMapedUser;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Kutia\Larafirebase\Facades\Larafirebase;

if (!function_exists('getDepartment')) {
  function getDepartment($id)
  {
    return Department::find($id);
  }
}

function getRole($id)
{
  return Role::find($id);
}
function getPosition($id)
{
  return Position::find($id);
}

function imagePath($image)
{
  if ($image == '' || $image == null) {
    return asset('https://static.vecteezy.com/system/resources/previews/008/442/086/original/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg');
  }
  $path = asset('storage/' . $image);
  return $path;
}


function deleteOldImage($path)
{
  if ($path) {
    $storage = Storage::delete('public/' . $path);
  }
  return true;
}
function dateformat($date, $format)
{
  return Carbon::create($date)->format($format);
}
function countTask($user_id, $status, $date)
{
  if ($status == 'T') {
    $count = ManageTask::where('user_id', $user_id)->where('status', '=' ,'to-do')
               ->where(function($query) use ($date){
                    $query->where('startdate','=',$date)
                   ->orWhere('enddate','=',$date);
               })->get()->count();
              // ->where(function($query2) use ($variable1,$variable2){
              //       $query2->where('column4','=',$variable1)
              //      ->where('column5','=',$variable2);
              // })->get();

    return $count;
  }
  if ($status == 'O') {
    $count = ManageTask::where('user_id', $user_id)->withCount([
      'manageprocess',
      'manageprocess as manageprocess_count' => function ($query) use ($date) {
        $query->where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '2');
      }
    ])->get()->pluck('attendance_count')->sum();
    // $count = Attendance::where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '2')->count();
  }

  return $count;
}
// for daily attendance cards
function countAttendance($date, $status, $emp_type)
{
  if ($status == 'P') {
    $count = User::where('emp_type', $emp_type)->withCount([
      'attendance',
      'attendance as attendance_count' => function ($query) use ($date) {
        $query->where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '1');
      }
    ])->get()->pluck('attendance_count')->sum();
    return $count;
  }
  if ($status == 'A') {
    $count = User::where('emp_type', $emp_type)->withCount([
      'attendance',
      'attendance as attendance_count' => function ($query) use ($date) {
        $query->where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '2');
      }
    ])->get()->pluck('attendance_count')->sum();
    // $count = Attendance::where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '2')->count();
  }
  if ($status == 'HD') {
    $count = User::where('emp_type', $emp_type)->withCount([
      'attendance',
      'attendance as attendance_count' => function ($query) use ($date) {
        $query->where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '3');
      }
    ])->get()->pluck('attendance_count')->sum();
    // $count = Attendance::where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '3')->count();
  }
  if ($status == 'L') {
    $count = User::where('emp_type', $emp_type)->withCount([
      'attendance',
      'attendance as attendance_count' => function ($query) use ($date) {
        $query->where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '4');
      }
    ])->get()->pluck('attendance_count')->sum();
    // $count = Attendance::where('date', dateformat($date, 'Y-m-d'))->where('is_approved', '4')->count();
  }
  return $count;
}
function countTotalP($user_id, $active_month)
{
  $total_P = Attendance::where('user_id', $user_id)->where('date', 'LIKE', $active_month . '%',)->where('is_approved', '1')->count();
  $total_HD = Attendance::where('user_id', $user_id)->where('date', 'LIKE', $active_month . '%',)->where('is_approved', '3')->count();
  return $total_P + ($total_HD / 2);
}

function getFilterMonths()
{
  if (Attendance::all()->count()) {
    $min_month = Carbon::create(Attendance::orderBy('date')->first('date')->date);
    $min_month = Carbon::create('2023-01-01');
    $max_month = Carbon::create(Attendance::orderBy('date', 'desc')->first('date')->date);
    $result = CarbonPeriod::create($min_month, '1 month', $max_month);
    return $result;
  }
  return [];
}

// get months with and date of active month
function getDateAndDays($active_month, $format)
{
  $month = Carbon::create($active_month)->format('m'); // The month you want to get the days names for
  $year = Carbon::create($active_month)->format('Y'); // The year you want to get the days names for
  $dates = [];
  for ($i = 1; $i <= Carbon::create($year, $month, 1)->daysInMonth; $i++) {
    $dates[] = Carbon::create($year, $month, $i)->format($format);
  }
  return $dates;
}

// monthly attendance counting month wise

function countMonhtlyAttendance($user_id, $month, $status)
{
  // dd($month);
  $count = Attendance::where('user_id', $user_id)->where('date', 'LIKE', $month . '%')->where('is_approved', $status)->count();
  return $count;
}

function getCategories()
{
  $data = Categories::where('is_active', true)->whereNull('parent_id')->get();
  return $data;
}

function getCategoryById($id)
{
  $data = Categories::find($id);
  return $data;
}


function getCompanies()
{
  $data = Companie::where('is_active', true)->get();
  return $data;
}
function getSubCategories()
{
  $data = Categories::where('is_active', true)->whereNotNull('parent_id')->get();
  return $data;
}
function getSubCategory($parent_id)
{
  $data = Categories::where('is_active', true)->where('parent_id', $parent_id)->get();
  return $data;
}


function getWarehouses()
{
  $data = Warehouse::where('is_active', true)->get();
  return $data;
}

function getActiveDepos()
{
  $data = Depo::where('is_active', true)->get();
  return $data;
}

function getVendors()
{
  $data = Vendor::where('is_active', true)->get();
  return $data;
}



function getVehicleManufacturer()
{
  $data = VehicleManufacturer::where('is_active', true)->orderBy('name')->get();
  return $data;
}

function getVehiclesModels($id)
{
  $data = VehicleModel::where('manufacturer_id', $id)->orderBy('name')->get();
  return $data;
}

function getVehicles($id)
{

  $data = Vehicles::where('manufacturer_id', $id)->orderBy('name')->get();
  return $data;
}
function getProducts()
{
  $data = ProductMaster::where('is_active', true)->orderBy('name')->get();
  return $data;
}
function getAllProducts()
{
  $data = ProductMaster::orderBy('name')->get();
  return $data;
}

function getProduct($id)
{
  $data = ProductMaster::find($id);
  return $data;
}
function getUOM()
{
  $data = Uom::where('is_active', true)->get(['id', 'name']);
  // $data = ['Kilogram', 'Units', 'Gram (g)', 'Meter', 'Litre', 'Foot (feet)', 'Inches (inch)'];
  return $data;
}

function getActiveVehicles()
{
  $data = VehicleUser::where('is_active', true)->where('is_on_trip', false)->with('vehicle')->get();
  return $data;
}
function getUsersByRoleName($rolesArray)
{
  $roles = Role::where('is_active', true)->whereIn('name', $rolesArray)->get()->groupBy('id')->toArray();

  if (!empty($roles)) {
    $roleIds = array_keys($roles);

    $users = User::whereIn('role_id', $roleIds)->where('is_active', true)->get();

    if ($users->count()) {
      return $users;
    }

    return [];
  }

  return [];
}


function getActiveDivisions()
{
  $data = Division::where('is_active', true)->with('sub_divisions')->get();
  if ($data->count()) {
    return $data;
  }
  return [];
}
function getSubDivision($id)
{
  $data = SubDivision::find($id);
  if ($data) {
    return $data;
  }
  return null;
}
function getActiveExpenses()
{
  $data = Expense::get();
  if ($data) {
    return $data;
  }
  return [];
}

function getMappedUserData()
{
  $user = Auth::user();
  $mappedUser = WhDpMapedUser::where('user_id', $user->id)->whereNull('deassigned_at')->get();

  return $mappedUser;
}

function getMappedUserDataByUserId($user_id)
{
  $user = User::find($user_id);
  $mappedUser = WhDpMapedUser::where('user_id', $user->id)->whereNull('deassigned_at')->get();

  return $mappedUser;
}

function getInventoryTypeBySlug($slug)
{
  $type = InventoryType::where('slug', $slug)->first();
  if ($type) {
    return $type->id;
  }
}
function getProjectManagementTypeBySlug($slug)
{
  $type = ProjectManagementType::where('slug', $slug)->first();
  if ($type) {
    return $type->id;
  }
}
function dateDifference($validFrom, $validTo)
{
  $fromDate = new DateTime($validFrom);
  $toDate = new DateTime($validTo);
  $difference = $fromDate->diff($toDate);

  return $difference->days;
}

function getStatesAndUT()
{
  return [
    'Andhra Pradesh',
    'Arunachal Pradesh',
    'Assam',
    'Bihar',
    'Chhattisgarh',
    'Goa',
    'Gujarat',
    'Haryana',
    'Himachal Pradesh',
    'Jharkhand',
    'Karnataka',
    'Kerala',
    'Madhya Pradesh',
    'Maharashtra',
    'Manipur',
    'Meghalaya',
    'Mizoram',
    'Nagaland',
    'Odisha',
    'Punjab',
    'Rajasthan',
    'Sikkim',
    'Tamil Nadu',
    'Telangana',
    'Tripura',
    'Uttar Pradesh',
    'Uttarakhand',
    'West Bengal',
    'Andaman and Nicobar Islands',
    'Chandigarh',
    'Dadra and Nagar Haveli and Daman and Diu',
    'Delhi',
    'Lakshadweep',
    'Puducherry'
  ];
}

function getPolicyTypes()
{
  return [
    '1 Year Comprehensive',
    '2 Year Comprehensive',
    // 'Basic Coverage for 1 Year',
    // 'Extended Coverage for 2 Years',
    // // Add more policy types as needed
  ];
}
function getNPcategories()
{
  return [
    'Goods Carriage - National Permit',
    'Contract Carriage - National Permit',
    'Tourist Vehicle - National Permit',
    'Stage Carriage - National Permit',
  ];
}
function getVehicleBodyTypes()
{
  return [
    "Full body Truck",
    "Half body Truck",
    "Platform Truck",
    "Container Body",
    "Gas Cascade Body",
    "Cryogenic Capsule",
    "Fuel & Chemical Tanker",
    "Milk/Water Tanker",
    "Tipper Body",
    "Special Application Body",
  ];
}
function getVehicleColors()
{
  return [
    "Red",
    "Blue",
    "Green",
    "Yellow",
    "Black",
    "White",
    "Silver",
    "Gray",
    "Orange",
    "Purple",
  ];
}
function getFuelStations()
{
  $fuelStations = [
    "Indian Oil",
    "Bharat Petroleum",
    "Hindustan Petroleum",
    "Reliance Petroleum",
    "Essar Oil",
    "Shell",
    "GAIL",
    "HPCL-Mittal Energy Limited",
    "Nayara Energy",
  ];
  return $fuelStations;
}


if (!function_exists('getDummyProducts')) {
  function getDummyProducts()
  {
    $products = ProductMaster::paginate(10);

    return $products;
  }
}
// if (!function_exists('checkMinStockLevelOrSendNotification')) {
//   function checkMinStockLevelOrSendNotification($product_id, $source_id, $source_type_id)
//   {
//     try {
//       $product = Inventory::where(['product_id' => $product_id, 'source_id' => $source_id, 'inventory_type_id' => $source_type_id])->first();
//       if ($product) {
//         $productName = $product->product->name; // Assuming this is how you access the product's name
//         $sourceName = $product->source()->name; // Example, adjust based on your actual method to get the source

//         // Initialize the admin user to send notifications to
//         $admin = User::whereNull('role_id')->first(); // Adjust if your logic to select the admin differs

//         if ($product->quantity == 0) {
//           // Product is out of stock
//           $title = "$productName - Out of Stock in $sourceName";
//           $message = "$productName is out of stock in $sourceName.";
//           $data = [
//             'title' => $title,
//             'message' => $message,
//           ];

//           // Create and send the notification
//           $notification = new SendPushNotification($title, $message, $admin, $data);
//           $admin->notify($notification);

//           // Web notification
//           // Adjust the route as necessary for your application
//           $admin->notify(new WebNotification(route('product.details', $product->id), $title, $message));
//         } elseif ($product->inventory_type_id == getInventoryTypeBySlug('warehouse') && $product->quantity <= $product->product->min_stock_warehouse) {
//           // Product is below minimum stock level in warehouse
//           $minStock = $product->product->min_stock_warehouse;
//           $title = "$productName - Low Stock in $sourceName";
//           $message = "$productName in $sourceName has fallen below the minimum stock level of $minStock.";
//           $data = [
//             'title' => $title,
//             'message' => $message,
//           ];

//           // Create and send the notification
//           $notification = new SendPushNotification($title, $message, $admin, $data);
//           $admin->notify($notification);

//           // Web notification
//           // Adjust the route as necessary for your application
//           $admin->notify(new WebNotification(route('product.details', $product->id), $title, $message));
//         } elseif ($product->inventory_type_id == getInventoryTypeBySlug('depot') && $product->quantity <= $product->product->min_stock_depo) {
//           // Product is below minimum stock level in depot
//           $minStock = $product->product->min_stock_depo;
//           $title = "$productName - Low Stock in $sourceName";
//           $message = "$productName in $sourceName has fallen below the minimum stock level of $minStock.";
//           $data = [
//             'title' => $title,
//             'message' => $message,
//           ];

//           // Create and send the notification
//           $notification = new SendPushNotification($title, $message, $admin, $data);
//           $admin->notify($notification);

//           // Web notification
//           // Adjust the route as necessary for your application
//           $admin->notify(new WebNotification(route('product.details', $product->id), $title, $message));
//         }else{
//           return;
//         }
//       }
//     } catch (\Exception $e) {
//     }
//   }
// }


if (!function_exists('checkMinStockLevelOrSendNotification')) {
  function checkMinStockLevelOrSendNotification($product_id, $source_id, $source_type_id)
  {
      try {
          $product = Inventory::with('product')
              ->where(['product_id' => $product_id, 'source_id' => $source_id, 'inventory_type_id' => $source_type_id])
              ->first();

          if (!$product) {
              return;
          }

          $productName = $product->product->name;
          $sourceName = $product->source()->name; // Assuming 'source' is correctly loaded and has 'name' attribute
          
          $admin = User::whereNull('role_id')->first();
          if (!$admin) {
              return;
          }

          $minStock = 0;
          $stockConditionMet = false;
          switch ($source_type_id) {
              case getInventoryTypeBySlug('warehouse'):
                  $minStock = $product->product->min_stock_warehouse;
                  $stockConditionMet = $product->quantity <= $minStock;
                  break;
              case getInventoryTypeBySlug('depot'):
                  $minStock = $product->product->min_stock_depo;
                  $stockConditionMet = $product->quantity <= $minStock;
                  break;
          }

          if ($product->quantity == 0 || $stockConditionMet) {
              $status = $product->quantity == 0 ? 'Out of Stock' : 'Low Stock';
              $title = "$productName - $status in $sourceName";
              $message = $product->quantity == 0 ? "$productName is out of stock in $sourceName." : "$productName in $sourceName has fallen below the minimum stock level of $minStock.";
              $route=route('admin.inventory.index', ['productsNames[]' => $product->product_id, 'source_ids[]' =>  json_encode(['id' => $product->source_id, 'inventory_type_id' => $product->inventory_type_id])]);
              sendNotification($admin, $title, $message, $route); // Refactored notification logic
          }
      } catch (\Exception $e) {
        return dd($e);
          // Consider logging the exception
      }
  }
}

function sendNotification($user, $title, $message, $route)
{
  $data = [
      'title' => $title,
      'message' => $message,
  ];

  // Create and send the notification
  $notification = new SendPushNotification($title, $message, $user, $data);
  $user->notify($notification);

  // Web notification
  $user->notify(new WebNotification($route, $title, $message));
  // dd($notification);
}
