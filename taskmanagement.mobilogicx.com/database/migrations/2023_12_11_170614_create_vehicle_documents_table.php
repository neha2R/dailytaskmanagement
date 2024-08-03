<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained('vehicles');
            
            // Descriptive name for the document
            $table->string('document_name');

            // Type of the document (e.g., license, registration, insurance)
            $table->string('document_type', 10);

            // Abbreviated or short name for the document type
            $table->string('document_short_name', 80)->nullable();

            // Document number
            $table->string('document_number');

            // Validity period: From and To dates
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();

            // Date of vehicle registration (if applicable)
            $table->dateTime('registration_date')->nullable();

            // Issuer of the document
            $table->string('issuer_name')->nullable();

            // Path to the stored document file
            $table->string('document_path')->nullable();

            // Flag indicating whether the document has been renewed
            $table->boolean('is_renewed')->default(false);

            // Soft deletes for handling deleted records
            $table->softDeletes();

            // Standard timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_documents');
    }
}
