@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}" />
@endsection

@section('content')
    <div class="confirm__content">
        <div class="confirm__heading">
            <h2>お問い合わせ内容確認</h2>
        </div>
        <div class="form-card">
            {{-- フォーム全体 (submit/backの両方に対応) --}}
            <form class="form" action="{{ route('store') }}" method="post">
                @csrf
                <div class="confirm-table">
                    <table class="confirm-table__inner">
                        <tr class="confirm-table__row">
                            <th class="confirm-table__header">お名前</th>
                            <td class="confirm-table__text">
                                <span>{{ $contact['name'] }}</span>
                                <input type="hidden" name="name" value="{{ $contact['name'] }}" readonly />
                            </td>
                        </tr>
                        <tr class="confirm-table__row">
                            <th class="confirm-table__header">メールアドレス</th>
                            <td class="confirm-table__text">
                                <span>{{ $contact['email'] }}</span>
                                <input type="hidden" name="email" value="{{ $contact['email'] }}" readonly />
                            </td>
                        </tr>
                        <tr class="confirm-table__row">
                            <th class="confirm-table__header">電話番号</th>
                            <td class="confirm-table__text">
                                <span>{{ $contact['tel'] }}</span>
                                <input type="hidden" name="tel" value="{{ $contact['tel'] }}" readonly />
                            </td>
                        </tr>
                        <tr class="confirm-table__row confirm-table__row--content">
                            <th class="confirm-table__header">お問い合わせ内容</th>
                            <td class="confirm-table__text confirm-table__text--content">
                                <span>{{ $contact['content'] }}</span>
                                <input type="hidden" name="content" value="{{ $contact['content'] }}" readonly />
                            </td>
                        </tr>
                        <tr class="confirm-table__row confirm-table__row--last">
                            <th class="confirm-table__header">添付ファイル</th>
                            <td class="confirm-table__text">
                                <!-- Base64 URL (image_preview_url)が存在する場合にプレビューとファイル名を表示 -->
                                @if (!empty($contact['image_preview_url']))
                                    <div style="margin-bottom: 15px; border: 1px solid #eee; padding: 10px; border-radius: 6px;">
                                        <p style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">ファイル名: {{ $contact['image_file_name'] ?? '画像ファイル' }}</p>
                                        <img src="{{ $contact['image_preview_url'] }}"
                                             alt="添付画像プレビュー"
                                             style="max-width: 200px; height: auto; border-radius: 4px; border: 1px solid #ddd;">
                                    </div>

                                    <!-- Base64データとファイル名を hidden で戻す/最終送信のために渡す -->
                                    <input type="hidden" name="image_preview_url" value="{{ $contact['image_preview_url'] }}" readonly />
                                    <input type="hidden" name="image_file_name" value="{{ $contact['image_file_name'] ?? '' }}" readonly />

                                    {{-- サーバーの一時パスも戻る際に必要なので追加します --}}
                                    <input type="hidden" name="image_file_path" value="{{ $contact['image_file_path'] ?? '' }}" readonly />
                                @else
                                    <span>(添付ファイルなし)</span>
                                    <input type="hidden" name="image_preview_url" value="" readonly />
                                    <input type="hidden" name="image_file_name" value="" readonly />
                                    <input type="hidden" name="image_file_path" value="" readonly />
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="form__actions">
                    <!-- 「編集」ボタンを押すと、この隠しデータ（image_file_pathを含む）がセッションに復元され、index画面で画像が再表示されます -->
                    <button class="form__button-edit" type="submit" formaction="{{ route('back') }}" formmethod="post">
                        編集
                    </button>
                    <button class="form__button-submit" type="submit">
                        送信
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
