@extends('layouts.app')

@section('content')

<header class="bg-white shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        
        <a href="#" class="flex items-center space-x-2">
            <img src="{{ asset('img/budgeting.png') }}" alt="Logo" class="h-8 w-8">
            <span class="font-bold text-2xl text-blue-600">- Retribusi</span>
        </a>
        
        <nav class="hidden md:flex space-x-6">
            <a href="#fitur" class="text-gray-600 hover:text-blue-600">Fitur</a>
            <a href="#harga" class="text-gray-600 hover:text-blue-600">Harga</a>
            <a href="#kontak" class="text-gray-600 hover:text-blue-600">Kontak</a>
        </nav>
        
        <a href="#" class="hidden md:block bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            Mulai Sekarang
        </a>
    </div>
</header>

<section class="bg-blue-50 py-20">
    
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 leading-tight mb-6">
            e-Retribusi
        </h1>
        <p class="text-lg text-gray-700 mb-8 max-w-4xl mx-auto">
            E-retribusi adalah sistem pembayaran retribusi secara elektronik atau non-tunai yang menggantikan metode manual. Sistem ini dirancang untuk mempermudah, mempercepat, dan meningkatkan transparansi dalam pengelolaan serta pembayaran pungutan daerah.
        </p>
        <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex items-center space-x-2 bg-blue-600 text-white text-lg font-semibold px-8 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition">
    
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
</svg>


    <span>Login</span>
</a>
    </div>
</section>

<section id="fitur" class="py-20">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
            Kenapa Memilih Kami?
        </h2>
        
        <div class="grid md:grid-cols-3 gap-10">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <h3 class="text-xl font-bold mb-2">Cepat & Handal</h3>
                <p class="text-gray-600">
                    Dengan e-Retribusi, Anda dapat mengakses dan mengelola pembayaran retribusi secara online. Anda dapat melihat daftar retribusi yang harus dibayarkan, melakukan pembayaran dengan metode yang tersedia, serta melacak status pembayaran retribusi tersebut.
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <h3 class="text-xl font-bold mb-2">Sangat Aman</h3>
                <p class="text-gray-600">
                    Pada halaman e-Retribusi, Anda dapat mengelola pembayaran retribusi dengan mudah dan efisien. e-Retribusi adalah sistem yang dirancang untuk menggantikan proses manual pembayaran retribusi dengan menggunakan teknologi digital.
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h3 class="text-xl font-bold mb-2">Mudah Digunakan</h3>
                <p class="text-gray-600">
                    Sistem e-Retribusi memberikan keuntungan dalam hal efisiensi dan penghematan waktu. Anda tidak perlu lagi mengantri dan membayar retribusi secara fisik, serta mengelola bukti pembayaran manual. Semua proses pembayaran retribusi dapat dilakukan secara elektronik, sehingga meminimalkan risiko kesalahan dan mempercepat proses pembayaran.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="kontak" class="bg-blue-600 text-white py-20">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">
            Siap untuk Memulai?
        </h2>
        <p class="text-lg text-blue-100 mb-8 max-w-lg mx-auto">
            Daftar sekarang dan dapatkan akses penuh ke semua fitur kami.
        </p>
        <a href="https://pkm-cikelet.garutkab.go.id/" class="bg-white text-blue-600 text-lg font-semibold px-8 py-3 rounded-lg shadow-lg hover:bg-gray-100 transition">
            Hubungi Admin !
        </a>
    </div>
</section>

<footer class="bg-gray-800 text-gray-400 py-10">
    <div class="container mx-auto px-6 text-center">
        <p>&copy; {{ date('Y') }} UPT Puskesmas Cikelet. Semua Hak Dilindungi.</p>
    </div>
</footer>

@endsection