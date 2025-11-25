<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Form</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__container">
            <div class="header__inner">
                <a class="header__logo" href="{{ route('index') }}">
                    お問合せフォーム
                </a>
            </div>
            <nav class="header__nav-items">
                @auth
                <a class="header__nav-item" href="{{ route('contact.history') }}">送信履歴</a>
                <form class="header__nav-item" action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="header__nav-item--button" type="submit">ログアウト</button>
                </form>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="main-content">
            @yield('content')
        </div>
    </main>
</body>

</html>
