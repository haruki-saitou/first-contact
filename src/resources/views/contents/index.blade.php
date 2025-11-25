@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
    <div class="contact-form__content">
        <div class="contact-form__heading">
            <h2>お問い合わせ</h2>
        </div>
        <div class="form-card">
            {{-- フォームは confirm ルートに送信されます --}}
            <form class="form" action="{{ route('confirm') }}" method="post" enctype="multipart/form-data">
                @csrf

                {{-- ** 解決策の核 **: 戻るボタンで復元された一時ファイル情報を保持するhiddenフィールド --}}
                {{-- confirm画面から戻ったとき、これらの値がセッションからold()で復元されます。 --}}
                {{-- これを送信することで、再確認時もデータがサーバーに残っていることをコントローラに伝えます。 --}}
                <input type="hidden" name="image_preview_url_old" value="{{ old('image_preview_url') }}" />
                <input type="hidden" name="image_file_name_old" value="{{ old('image_file_name') }}" />
                <input type="hidden" name="image_file_path_old" value="{{ old('image_file_path') }}" />


                {{-- 氏名 --}}
                <div class="form-group">
                    <label for="name" class="form-label">お名前 <span class="required-star">*</span></label>
                    <input type="text" id="name" name="name"
                        class="form-input @error('name') form-input--error @enderror" value="{{ old('name') }}"
                        placeholder="山田 太郎" />
                    @error('name')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- メールアドレス --}}
                <div class="form-group">
                    <label for="email" class="form-label">メールアドレス <span class="required-star">*</span></label>
                    <input type="email" id="email" name="email"
                        class="form-input @error('email') form-input--error @enderror" value="{{ old('email') }}"
                        placeholder="test@example.com" />
                    @error('email')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 電話番号 --}}
                <div class="form-group">
                    <label for="tel" class="form-label">電話番号</label>
                    <input type="tel" id="tel" name="tel"
                        class="form-input @error('tel') form-input--error @enderror" value="{{ old('tel') }}"
                        placeholder="09012345678 (ハイフンなし)" />
                    @error('tel')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- お問い合わせ内容 --}}
                <div class="form-group">
                    <label for="content" class="form-label">お問い合わせ内容 <span class="required-star">*</span></label>
                    <textarea id="content" name="content" rows="6" class="form-input @error('content') form-input--error @enderror"
                        placeholder="お問い合わせ内容をご記入ください">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 添付ファイル --}}
                <div class="form-group file-group">
                    <label class="form-label">添付ファイル (任意)</label>

                    {{-- プレビューエリア --}}
                    <div id="image-preview-container" class="image-preview-area">
                        {{-- 戻った際に画像がある場合の初期プレビュー --}}
                        @if (old('image_preview_url'))
                            <div id="current-image-wrapper" class="current-image-preview-wrapper" style="display: flex;">
                                <p class="current-image-info">復元された画像ファイル: {{ old('image_file_name') }}</p>
                                <img id="current-image-img" src="{{ old('image_preview_url') }}" alt="添付画像プレビュー"
                                    class="current-image">
                            </div>
                        @else
                            <div id="current-image-wrapper" class="current-image-preview-wrapper" style="display: none;">
                                <p class="current-image-info">復元された画像ファイル:</p>
                                <img id="current-image-img" src="#" alt="添付画像プレビュー" class="current-image">
                            </div>
                        @endif

                        {{-- 新しいファイル選択時のプレビュー (最初は非表示) --}}
                        <div id="new-image-preview-wrapper" class="current-image-preview-wrapper" style="display: none;">
                            <p class="current-image-info">選択された新しいファイル:</p>
                            <img id="new-image-preview-img" src="#" alt="新しい添付画像プレビュー" class="current-image">
                        </div>
                    </div>

                    {{-- ファイルアップロードコントロール --}}
                    <div class="custom-file-upload-wrapper">
                        <input type="file" id="image_file" name="image_file" accept="image/*"
                            class="native-file-input @error('image_file') form-input--error @enderror"
                            onchange="handleFileChange(this)">

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

                        <span class="file-name-display" id="file-name-display">
                            @if (old('image_file_name'))
                                {{ old('image_file_name') }}
                            @else
                                選択されていません
                            @endif
                        </span>
                    </div>

                    @error('image_file')
                        <p class="text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 確認ボタン --}}
                <div class="form__actions">
                    <button class="form__button-submit" type="submit">
                        確認
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 画像プレビューを制御するJavaScript --}}
    <script>
        const currentImageWrapper = document.getElementById('current-image-wrapper');
        const currentImageInfo = currentImageWrapper ? currentImageWrapper.querySelector('.current-image-info') : null;
        const currentImageImg = document.getElementById('current-image-img');

        const newImagePreviewWrapper = document.getElementById('new-image-preview-wrapper');
        const newImagePreviewImg = document.getElementById('new-image-preview-img');

        const fileNameDisplay = document.getElementById('file-name-display');
        const initialFileName = fileNameDisplay.textContent.trim(); // 復元されたファイル名を保持

        /**
         * ファイル選択時の処理 (onchangeで呼ばれる)
         * @param {HTMLInputElement} input - ファイルインプット要素
         */
        function handleFileChange(input) {
            const file = input.files[0];

            if (file) {
                // ファイルが選択された場合
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 新しいプレビューを表示
                    newImagePreviewImg.src = e.target.result;
                    newImagePreviewWrapper.style.display = 'flex';

                    // 復元された古いプレビューを非表示
                    currentImageWrapper.style.display = 'none';

                    // ファイル名表示を更新
                    fileNameDisplay.textContent = file.name;
                    fileNameDisplay.classList.add('is-selected');
                };
                reader.readAsDataURL(file);

            } else {
                // ファイル選択がキャンセルされた場合 (ファイル入力がクリアされた場合)

                // 1. 新しいプレビューを非表示
                newImagePreviewWrapper.style.display = 'none';
                newImagePreviewImg.src = '#';

                // 2. ファイル名表示をリセット
                fileNameDisplay.textContent = initialFileName || '選択されていません';
                if (initialFileName) {
                    fileNameDisplay.classList.add('is-selected');
                } else {
                    fileNameDisplay.classList.remove('is-selected');
                }

                // 3. 復元された古い画像があれば再表示（ファイル選択をキャンセルした場合は、old()の値が活きる）
                const oldImageUrl = document.querySelector('input[name="image_preview_url_old"]').value;
                if (oldImageUrl) {
                    currentImageImg.src = oldImageUrl;
                    currentImageInfo.textContent = '復元された画像ファイル: ' + document.querySelector(
                        'input[name="image_file_name_old"]').value;
                    currentImageWrapper.style.display = 'flex';
                } else {
                    currentImageWrapper.style.display = 'none';
                }
            }
        }

        // 初期ロード時のファイル名表示の調整
        document.addEventListener('DOMContentLoaded', function() {
            if (initialFileName && initialFileName !== '選択されていません') {
                fileNameDisplay.classList.add('is-selected');
            }
        });
    </script>
@endsection
