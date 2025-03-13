```php
    public function testJsonErrors(Request $request) {
        $isJsonError = $request->input('isJsonError') === "1";
        $isDomain = $request->input('isDomain') === "1";
        $debug = $request->input('debug') === "1";

        if (!$debug) {
            config(['app.debug' => false]);
        }

        if (!$isJsonError) {
            if ($isDomain) {
                abortC(400, 'error de dominio automatico');
            }
            throw new \Exception('error de laravel automatico');
            abort(500, 'error de laravel automatico');
        }

        try {

            if ($isDomain) {
                abortC(400, 'error de dominio con "responseJsonError"');
            }
            throw new \Exception('error de laravel responseJsonError');
            abort(400, 'error de laravel con "responseJsonError"');

            return response_json(true, 'success');
        } catch (Throwable $e) {
            return response_json_error($e);
        }

    }

    public function testFlash() {
//        return redirect()->route('home.example1')->with('error', 'aaa');
        return redirect()->route('home.example1')->with('severalErrors', ['titulo', 'primero', 'seg']);
    }
```