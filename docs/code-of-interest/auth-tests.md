```php
Route::get('/login-web/{id?}', function (int $id = 2) {
    $u = \Illuminate\Support\Facades\Auth::guard('web')->loginUsingId($id);
    return 'logueado';
});
Route::get('/login-api/{id?}', function (int $id = 1) {
    $u = \Illuminate\Support\Facades\Auth::guard('api')->loginUsingId($id);
    return 'logueado';
});

Route::get('/logout-web', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return 'des logueado';
});
Route::get('/logout-api', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('api')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return 'des logueado';
});
Route::get('/test', function () {

    // TestJob::dispatch(session()->id());

    /*dd(
        auth()->user()?->toArray(),
        auth('api')->user()?->toArray(),
        session()->all(),
    );*/
    dd(
        user()?->toArray(),
        user()?->toArray(),
        user()?->toArray(),
        user('api')?->toArray(),
        user('api')?->toArray(),
        user('api')?->toArray(),
    );
});
```

```php
// TEST JOB
public function __construct(
    private readonly string $previousSessionId
)
{
}

public function handle(): void
{
    Session::setId($this->previousSessionId);
    Session::start();

    dd(
        request()->all(),
        session()->all(),
        auth()->user()?->toArray(),
        user()?->toArray(),
    );
}
```