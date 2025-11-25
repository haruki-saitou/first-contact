@extends('layouts.app')

@section('css')

<link rel="stylesheet" href="{{ asset('css/complete.css') }}" />
@endsection

@section('content')

<div class="complete__content">
<div class="form-card">
<div class="complete__heading">
<svg xmlns="http://www.w3.org/2000/svg" class="complete__icon" viewBox="0 0 20 20">
<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
</svg>
<h2>お問い合わせ内容、送信完了しました。</h2>
</div>

    <div class="complete__button">
        <a class="complete__button-link" href="{{ route('index') }}">
            トップページへ戻る
        </a>
    </div>
</div>


</div>
@endsection
