# BE-Umazing - Backend E-commerce/Marketplace API

Aplikasi backend API untuk platform e-commerce/marketplace, dikembangkan menggunakan **Laravel Framework** dan **PHP**. API ini menyediakan semua *endpoint* yang diperlukan untuk otentikasi pengguna, manajemen produk, manajemen keranjang, dan pemrosesan pesanan, dengan pemisahan akses antara pengguna biasa (member) dan administrator (admin).

---

## Fitur Utama (API Endpoints)

API ini mendukung fungsionalitas untuk peran `user` (anggota) dan `admin`.

### 1. Autentikasi dan Umum
* **Pendaftaran (`/api/register`):** Membuat akun pengguna baru.
* **Login (`/api/login`):** Mengautentikasi pengguna dan mengembalikan token otorisasi (**Laravel Sanctum**).
* **Profil (`/api/me`):** Mengambil data pengguna yang sedang login.
* **Logout (`/api/logout`):** Menghapus token otorisasi pengguna.

### 2. Fitur Publik & Member
* **Produk (`/api/product`):** Melihat daftar dan detail produk (tersedia untuk umum).
* **Keranjang Belanja (`/api/cart`):** Menambah, memperbarui jumlah, dan menghapus item dari keranjang.
* **Checkout & Pemesanan (`/api/order`):** Membuat pesanan baru dari item di keranjang.
* **Daftar Pesanan (`/api/orders`):** Melihat riwayat pesanan pengguna.

### 3. Fitur Administrator (Admin)
* **Manajemen Kategori:** Operasi CRUD untuk kategori produk (`/api/category`).
* **Manajemen Produk:** Operasi CRUD untuk produk, termasuk upload gambar ke storage (`/api/product`).
* **Manajemen Pesanan:** Memperbarui status pesanan (`/api/order/{id}`).
* **Statistik Dashboard (`/api/stats`):** Mendapatkan statistik total penjualan, produk terjual, dan produk terlaris.

---

## Teknologi yang Digunakan

| Kategori | Teknologi | Versi | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Framework** | Laravel | 12.x | Framework PHP untuk membangun API. |
| **Bahasa Pemrograman** | PHP | 8.2+ | Bahasa inti yang digunakan. |
| **Autentikasi API** | Laravel Sanctum | ^4.2 | Digunakan untuk otentikasi berbasis token SPA/API. |
| **Database** | MySQL / SQLite | - | Mendukung driver database standar Laravel. |
| **Dependency Manager** | Composer | - | Untuk mengelola library dan dependensi PHP. |

---

## Prasyarat Instalasi

Pastikan Anda memiliki perangkat lunak berikut:

* **PHP** (Versi **8.2** atau lebih tinggi).
* **Composer**
* **Database** (MySQL direkomendasikan, atau SQLite untuk pengembangan lokal).

## Instalasi dan Penggunaan

### 1. Klon Repositori

```bash
git clone <URL_REPOSITORI_ANDA>
cd be-umazing

### 2. install dependencies 
composer install

### 3. env configuration
cp .env.example .env
php artisan key:generate

example : 
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=backend_umazing
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_STATEFUL_DOMAINS="localhost:5173,127.0.0.1:5173,localhost,127.0.0.1:8000"

### 4. Database migrate
php artisan migrate
php artisan db:seed # Akan membuat user admin@umazing.com:admin123

### 5. run server
php artisan serve