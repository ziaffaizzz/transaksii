<?php
class Login extends Controllers
{
    // * tampilan login 
    public function index()
    {
        // * jika sudah login ,maka tidak usah masuk ke url login kembali
        if (isset($_SESSION["data"]["nama"])) {
            header("location:" . BASEURL . "dashboard");
        }
        $this->data["title"] = "Halaman Login - Djabenk";
        $this->view("login/index", $this->data);
    }

// * method untuk cek login username dan password [hash]
    public function ceklogin()
    {
        $username = htmlentities(strip_tags(trim($_POST["username"])));
        $password = htmlentities(strip_tags(trim($_POST["password"])));
        $remember = $_POST["remember"] ?? false;

        $row = $this->model("Login_model")->getByUsername($username);

        $usernameError = "";
        $passwordError = "";

        // Cek jika data user ditemukan
        if (!$row) {
            $usernameError .= "username tidak ditemukan";
        } else {
            // Cek password hash atau plain text
            $password_hash = $row["password"];
            
            // Coba verifikasi via password_verify (Bcrypt) atau pencocokan langsung
            if (!password_verify($password, $password_hash) && $password !== $password_hash) {
                $passwordError .= "password salah";
            }
        }

        // * jika username dan password tidak ada error
        if ($usernameError === "" && $passwordError === "") 
        {
            $data = [
                "nama" => $row["nama_user"],
                "tipe" => $row["tipe_user"]
            ];

            $_SESSION["data"] = $data;

            $response = [
                "status" => "berhasil"
            ];

            if ($remember == "true") 
            {
                setcookie("username", $username, time() + 60 * 60 * 24 * 7, "/");
                setcookie("password", $password, time() + 60 * 60 * 24 * 7, "/");
            } else {
                setcookie("username", "");
                setcookie("password", "");
            }
        } 
        else 
        {
            $response = [
                "status" => "gagal",
            ];
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    // * method untuk user keluar
    public function keluar()
    {
        session_destroy();
        $pesan = "Anda telah berhasil keluar";
        $pesan = urlencode($pesan);
        header("location:" . BASEURL . "$pesan");
    }
}
