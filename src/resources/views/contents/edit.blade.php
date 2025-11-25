@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}" />
@endsection

@section('content')
    <div class="edit-container">

        <h1 class="edit-title">お問い合わせ内容の編集</h1>

        {{-- 1. バリデーションエラーメッセージの表示 --}}
        @if ($errors->any())
            <div class="alert-error">
                <p>入力内容にエラーがあります。</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 2. 編集フォーム (contact.update) --}}
        <form action="{{ route('contact.update', $contact->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- PUTからPATCHに変更（より適切なRESTfulセマンティクス） --}}
            @method('PATCH')

            {{-- 既存画像パスを保持する隠しフィールド --}}
            @php
                // validationエラーで戻った際 (old) または初回表示時 ($contact->image_file_path) のパスを取得
                // サーバー側で既存画像を維持するために、この値を常に送信する
                $currentImagePath = old('current_image_path', $contact->image_file_path);
            @endphp
            <input type="hidden" name="current_image_path" value="{{ $currentImagePath }}">

            {{-- 氏名 --}}
            <div class="form-group">
                <label for="name" class="form-label">氏名</label>
                {{-- name は更新しない想定のため readonly --}}
                <input type="text" id="name" class="form-input" value="{{ old('name', $contact->name) }}" readonly>
                <input type="hidden" name="name" value="{{ old('name', $contact->name) }}">
            </div>

            {{-- メールアドレス --}}
            <div class="form-group">
                <label for="email" class="form-label">メールアドレス <span class="required-star">*</span></label>
                <input type="email" id="email" name="email"
                    class="form-input @error('email') form-input--error @enderror"
                    value="{{ old('email', $contact->email) }}">
                @error('email')
                    <p class="text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 電話番号 --}}
            <div class="form-group">
                <label for="tel" class="form-label">電話番号</label>
                <input type="tel" id="tel" name="tel"
                    class="form-input @error('tel') form-input--error @enderror" value="{{ old('tel', $contact->tel) }}">
                @error('tel')
                    <p class="text-error">{{ $message }}</p>
                @enderror
                <p class="form-note">例: 09012345678 (ハイフンなし)</p>
            </div>

            {{-- お問い合わせ内容 --}}
            <div class="form-group">
                <label for="content" class="form-label">お問い合わせ内容 <span class="required-star">*</span></label>
                <textarea id="content" name="content" rows="6" class="form-input @error('content') form-input--error @enderror"
                    required>{{ old('content', $contact->content) }}</textarea>
                @error('content')
                    <p class="text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 画像管理セクション --}}
            <div class="image-management-section">
                <h3 class="image-management-title">添付画像の管理</h3>

                {{-- 画像プレビューコンテナ --}}
                <div id="image-preview-container">

                    {{-- 既存の画像プレビュー (画像が添付されており、かつ削除チェックが入っていない場合) --}}
                    @if ($currentImagePath)
                        <div id="current-image-wrapper" class="current-image-preview-wrapper"
                            style="display: {{ $currentImagePath && old('delete_image') != '1' ? 'flex' : 'none' }};">
                            <div style="display: flex; flex-direction: column; gap: 12px; flex-grow: 1;">
                                <p class="current-image-info" style="font-weight: 600;">現在の添付画像:</p>
                                {{-- Storage::url を使用して画像を表示 --}}
                                <img id="current-image-img" src="{{ Storage::url($currentImagePath) }}"
                                    alt="{{ $contact->image_file_name ?? '添付画像' }}" class="current-image">

                                {{-- 画像削除チェックボックス --}}
                                <label for="delete_image" class="delete-checkbox-group">
                                    <input type="checkbox" name="delete_image" id="delete_image" value="1"
                                        {{ old('delete_image') == '1' ? 'checked' : '' }}>
                                    現在の画像を削除する
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- 画像がない場合のテキスト --}}
                    <div id="no-image-text-wrapper" class="current-image-preview-wrapper" style="display: none;">
                        <p id="no-image-text" class="current-image-info">
                            現在、画像は添付されていません。
                        </p>
                    </div>

                    {{-- 新しい画像プレビュー要素 (onchangeで表示) --}}
                    <div id="new-image-preview-wrapper" class="current-image-preview-wrapper" style="display: none;">
                        <div style="display: flex; flex-direction: column; gap: 12px; flex-grow: 1;">
                            <p class="current-image-info" style="font-weight: 600; color: #4f46e5;">新しい添付画像プレビュー:</p>
                            <img id="new-image-preview-img" src="#" alt="新しい添付画像プレビュー" class="current-image" style="border: 2px dashed #a5b4fc;">
                        </div>
                    </div>

                </div>

                {{-- 新しい画像ファイルのアップロード --}}
                <div class="form-group upload-file-group">
                    <label class="form-label">新しい画像をアップロード</label>

                    {{-- カスタムファイルアップロードコンポーネント --}}
                    <div class="custom-file-upload-wrapper">
                        {{-- 実際のファイルインプットを隠す --}}
                        <input type="file" id="image_file" name="image_file" accept="image/*"
                            class="native-file-input @error('image_file') form-input--error @enderror"
                            onchange="handleFileChange(this)">

                        {{-- ファイル選択ボタンとして機能するラベル --}}
                        <label for="image_file" class="file-upload-button">
                            <span class="file-upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 14.899V20a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5.1" />
                                    <path d="M15 9l-3-3-3 3" />
                                    <path d="M12 2v14" />
                                </svg>
                            </span> ファイルを選択
                        </label>

                        {{-- ファイル名を表示するエリア --}}
                        <span class="file-name-display" id="file-name-display">
                            選択されていません
                        </span>
                    </div>

                    @error('image_file')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                    <p class="form-note">新しい画像を選択すると、既存の画像と置き換えられます。</p>
                </div>
            </div>

            {{-- ボタンエリア --}}
            <div class="button-area">
                {{-- 更新ボタン --}}
                <button type="submit" class="btn-update">
                    <span class="button-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                        </svg>
                    </span> 内容を更新する
                </button>

                {{-- 戻るボタン --}}
                <a href="{{ route('contact.history') }}" class="btn-back">
                    戻る
                </a>
            </div>
        </form>

        {{-- 3. 削除フォーム (contact.destroy) - 独立したフォームとして実装 --}}
        <div class="delete-section">
            <h2 class="delete-title">投稿の削除</h2>
            <p style="font-size: 14px; color: #4b5563;">この操作は元に戻せません。投稿と添付画像は完全に削除されます。</p>

            {{-- confirm() は iframe 環境では使用を避けるべきため削除し、注意書きを追記しています。 --}}
            <form action="{{ route('contact.destroy', $contact->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <span class="delete-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 6h18" />
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        </svg>
                    </span> この投稿を完全に削除する
                </button>
            </form>
            <p class="text-xs">※本来であれば削除前にカスタムモーダルで最終確認が必要です。</p>
        </div>
    </div>
@endsection

    {{-- 画像プレビューとロジックを制御するJavaScript --}}
    <script>
        // 既存画像があるかどうかを判定する変数
        const initialImagePath = "{{ $currentImagePath }}";
        const hasImagePath = initialImagePath.length > 0;

        // DOM要素の取得
        const currentImageWrapper = document.getElementById('current-image-wrapper');
        const newImagePreviewWrapper = document.getElementById('new-image-preview-wrapper');
        const newImagePreviewImg = document.getElementById('new-image-preview-img');
        const deleteCheckbox = document.getElementById('delete_image');
        const fileNameDisplay = document.getElementById('file-name-display');
        const noImageTextWrapper = document.getElementById('no-image-text-wrapper');
        const fileInput = document.getElementById('image_file');

        /**
         * 既存の画像、新しい画像、画像なしの表示状態を制御する
         */
        function updateDisplayBasedOnState() {
            const isDeleting = deleteCheckbox ? deleteCheckbox.checked : false;
            const hasNewFile = fileInput.files && fileInput.files.length > 0;

            // 1. 新しいファイルが選択されている場合
            if (hasNewFile) {
                if (currentImageWrapper) currentImageWrapper.style.display = 'none';
                if (noImageTextWrapper) noImageTextWrapper.style.display = 'none';
                newImagePreviewWrapper.style.display = 'flex';
                return;
            }

            // 2. 新しいファイルがなく、既存画像がある場合 (initialImagePathが存在し、かつ削除チェックが入っていない)
            if (hasImagePath && !isDeleting) {
                if (currentImageWrapper) currentImageWrapper.style.display = 'flex';
                if (noImageTextWrapper) noImageTextWrapper.style.display = 'none';
                newImagePreviewWrapper.style.display = 'none';
                return;
            }

            // 3. 画像がない場合 (最初から画像がない、または削除が選択されている)
            if (currentImageWrapper) currentImageWrapper.style.display = 'none';
            if (noImageTextWrapper) noImageTextWrapper.style.display = 'flex';
            newImagePreviewWrapper.style.display = 'none';
        }

        // ファイル選択時の処理 (onchangeで呼ばれる)
        function handleFileChange(input) {
            const file = input.files[0];

            if (file) {
                // ファイルが選択された場合
                const reader = new FileReader();
                reader.onload = function(e) {
                    newImagePreviewImg.src = e.target.result;
                    // 新しい画像を選択したので、画像削除チェックを解除
                    if (deleteCheckbox) deleteCheckbox.checked = false;

                    // 表示を更新
                    updateDisplayBasedOnState();

                    // ファイル名表示を更新
                    fileNameDisplay.textContent = file.name;
                    fileNameDisplay.classList.add('is-selected');
                };
                reader.readAsDataURL(file);

            } else {
                // ファイル選択がキャンセルされた場合 (ファイル入力がクリアされた場合)
                // プレビューをクリア
                newImagePreviewImg.src = '#';

                // 既存の画像の状態に基づいて表示を戻す
                updateDisplayBasedOnState();

                // ファイル名表示をリセット
                fileNameDisplay.textContent = '選択されていません';
                fileNameDisplay.classList.remove('is-selected');
            }
        }

        // 削除チェックボックス変更時の処理
        if (deleteCheckbox) {
            deleteCheckbox.addEventListener('change', function() {
                // 表示を更新
                updateDisplayBasedOnState();

                // 削除が選択されたら、新規ファイル入力をクリア
                if (this.checked) {
                    fileInput.value = '';
                    fileNameDisplay.textContent = '選択されていません';
                    fileNameDisplay.classList.remove('is-selected');
                }
            });
        }

        // 初期ロード時の処理
        document.addEventListener('DOMContentLoaded', function() {
            // エラーで戻ってきた場合も、適切な画像の状態を再表示
            updateDisplayBasedOnState();

            // ファイル入力はセキュリティ上の理由でクリアされているため、表示もリセット
            fileNameDisplay.textContent = '選択されていません';
            fileNameDisplay.classList.remove('is-selected');
        });
    </script
