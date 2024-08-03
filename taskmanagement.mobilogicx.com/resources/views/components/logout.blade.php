<div class="modal fade" id="sign-out">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ready to Sign Out?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out? Pressing "Logout" will end your current session.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Stay Here</button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="btn btn-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    href="{{ route('logout') }}">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>