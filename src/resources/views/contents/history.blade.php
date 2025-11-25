@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
@endsection

@section('content')
<div class="history-card">
    <h2 class="card-title">投稿履歴</h2>

    <!-- 成功/エラーメッセージ表示エリア -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($contacts->isEmpty())
        <div class="no-history-message">
            <p>まだ投稿履歴はありません。</p>
            {{-- 新規投稿リンクのスタイルを history.css で定義したクラスに置き換えます --}}
            <p><a href="{{ route('index') }}" class="link-to-new-post">新しいお問い合わせを投稿する</a></p>
        </div>
    @else
        <p class="history-count">{{ $contacts->count() }} 件の履歴があります。</p>

        @foreach ($contacts as $contact)
            <div class="contact-item">
                <div class="item-header">
                    <span class="item-id">ID: {{ $contact->id }}</span>
                    <span class="item-date">投稿日: {{ $contact->created_at->format('Y/m/d H:i') }}</span>
                    <!-- 「完了」ステータスラベルは削除済み -->
                </div>

                <div class="item-body">
                    <p class="item-content-preview">
                        {{ \Illuminate\Support\Str::limit($contact->content, 100) }}
                    </p>

                    @if ($contact->image_file_path)
                        <div class="item-image-preview">
                            <!-- Storage::url() をフルパスで記述し、画像を表示 -->
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($contact->image_file_path) }}" alt="添付画像">
                        </div>
                    @else
                        <div class="item-image-preview no-image">
                            <!-- SVGアイコン: 画像なしのプレースホルダー (CSSで白塗りつぶし) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="none"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        </div>
                    @endif
                </div>

                <!-- アクションボタンエリア -->
                <div class="item-actions">
                    <!-- 更新ボタン -->
                    <a href="{{ route('contact.edit', $contact->id) }}" class="btn-action btn-edit" title="編集">
                        <!-- SVGアイコン: ペン/編集 (CSSで白塗りつぶしになる) -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                    </a>

                    <!-- 削除ボタン (POST/DELETEリクエストを送信) -->
                    <form action="{{ route('contact.destroy', $contact->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" title="削除"
                                onclick="return confirm('本当にこの投稿を削除しますか？');">
                            <!-- SVGアイコン: ゴミ箱 (CSSで白塗りつぶしになる) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>

            </div>
        @endforeach
    @endif

</div>
@endsection
