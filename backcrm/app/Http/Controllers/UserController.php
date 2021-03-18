<?php
    namespace App\Http\Controllers;

    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    class UserController extends Controller
    {
        public function __construct()
        {
            $this->middleware('jwt', ['except' => ['login', 'insert']]);
        }

        public function insert(Request $request) {
            $email = $request->input('email', null);
            $password = $request->input('password', null);
            $name = $request->input('name', null);
            $pass = hash('sha256', $password);
            $user = new User();
            if (!empty($email)) {
                $user->name = $name;
                $user->email = $email;
                $user->password = $pass;
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha registrado correctamente'
                );
                $user->save();
            } else{
                $data = array(
                    'status' => 'success',
                    'code' => 400,
                    'message' => 'Ha ocurrido un error al registrar el usuario'
                );
            }

            return response()->json($data, $data['code']);
        }

        /**
         * Get a JWT via given credentials.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function login()
        {
            $credentials = request(['email', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);
        }
        /**
         * Get the authenticated User.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function me()
        {
            return response()->json(auth()->user());
        }
        public function payload()
        {
            return response()->json(auth()->payload());
        }
        /**
         * Log the user out (Invalidate the token).
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function logout()
        {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        }
        /**
         * Refresh a token.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function refresh()
        {
            return $this->respondWithToken(auth()->refresh());
        }
        /**
         * Get the token array structure.
         *
         * @param string $token
         *
         * @return \Illuminate\Http\JsonResponse
         */
        protected function respondWithToken($token)
        {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user(),
            ]);
        }
    }
