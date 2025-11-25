<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;


class ContactController extends Controller
{
    /**
     * お問い合わせフォーム表示 (index)
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $user = Auth::user();

        // $userが存在する場合にのみ、その属性を取得する
        $name = $user ? $user->name : '';
        $email = $user ? $user->email : '';
        $tel = $user ? $user->tel : '';

        // content は User モデルには存在しないため、old()データのみを頼る
        $content = old('content', '');

        // old()データを優先して取得
        $user_data = [
            'name' => old('name', $name),
            'email' => old('email', $email),
            'tel' => old('tel', $tel),
            'content' => $content, // old()または空文字
            'image_preview_url' => old('image_preview_url', null), // Base64 URLの復元
            'image_file_name' => old('image_file_name', null),       // ファイル名の復元
            'image_file_path' => old('image_file_path', null),       // 一時パスの復元
        ];

        // 🚨 注意: Blade側で old('image_file_path') を hidden で送信するため、
        // ここでは old() の値も取得できるように 'image_file_path' を追加しました。

        return view('contents.index', ['user_data' => $user_data]);
    }

    /**
     * 確認画面表示 (confirm)
     * @param \App\Http\Requests\ContactRequest $request
     * @return \Illuminate\View\View
     */
    public function confirm(ContactRequest $request): View
    {
        $validatedData = $request->validated();

        $validatedData['image_preview_url'] = null;
        $validatedData['image_file_path'] = null;
        $validatedData['image_file_name'] = null;


        // 1. 新しいファイルがアップロードされた場合 (最優先)
        if ($request->file('image_file') && $request->file('image_file')->isValid()) {
            $image = $request->file('image_file');

            // 一時ファイルとして保存 (storage/app/public/temp に保存される)
            // $imagePath は "public/temp/xxxx.jpg" の形式になる
            $imagePath = $image->store('public/temp');

            // Base64データを生成し、セッションに格納（確認画面表示用）
            try {
                $fileContents = Storage::get($imagePath);
                $mimeType = $image->getMimeType();
                $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);

                $validatedData['image_preview_url'] = $base64Data;
            } catch (\Exception $e) {
                Log::error("Failed to read temporary file: " . $e->getMessage());
                $imagePath = null;
            }

            $validatedData['image_file_path'] = $imagePath; // 一時パスをセッションに保存
            $validatedData['image_file_name'] = $image->getClientOriginalName();

        // 2. 新しいファイルのアップロードがなく、かつ古い一時ファイル情報がある場合 (これが復元ロジック)
        //    index.blade.php で送信された hidden フィールドを参照します
        } else if (
            $request->input('image_file_name_old') &&
            $request->input('image_preview_url_old') &&
            $request->input('image_file_path_old')
        ) {
            // セッションに保存されていた古いデータをそのまま復元し、次の確認画面に進む
            $validatedData['image_preview_url'] = $request->input('image_preview_url_old');
            $validatedData['image_file_name'] = $request->input('image_file_name_old');
            $validatedData['image_file_path'] = $request->input('image_file_path_old');

            // 🚨 補足: このパス (image_file_path_old) に対応する一時ファイルが
            // サーバーに存在するかはチェックしていません。セッションの寿命に依存します。
            // 通常、セッションが有効な間は一時ファイルは保持されます。

        }

        // image_file オブジェクト自体はセッションに保存しない
        if (isset($validatedData['image_file'])) {
            unset($validatedData['image_file']);
        }

        // Base64データも含む全てのデータをセッションに格納
        $request->session()->put('contact', $validatedData);

        return view('contents.confirm', ['contact' => $validatedData]);
    }

    /**
     * 永続保存 (store)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $contact = $request->session()->get('contact');

        if (is_null($contact)) {
            return redirect()->route('index');
        }

        $tempPath = $contact['image_file_path'] ?? null;
        $dbSavePath = null;

        // 【画像保存ロジック】
        if ($tempPath && Storage::exists($tempPath)) {
            // 1. 永続的なファイル名と保存先を決定 (public/contacts/ に移動)
            $finalName = basename($tempPath);
            // Storage::moveは、ディスク全体でパスを指定する必要があります。
            // public/contacts/xxxx.jpg の形式で移動します。
            $finalStoragePath = 'public/contacts/' . $finalName;

            // 2. 一時ファイルから永続的な場所へ移動
            if (Storage::move($tempPath, $finalStoragePath)) {
                // 3. DBに保存するのは 'public/' を含まない相対パス
                $dbSavePath = 'contacts/' . $finalName;
                $contact['image_file_path'] = $dbSavePath;
                $contact['image_file_name'] = $contact['image_file_name'] ?? $finalName;
            } else {
                Log::error("Failed to move temporary image file from $tempPath to $finalStoragePath");
                $contact['image_file_path'] = null;
                $contact['image_file_name'] = null;
            }
        } else {
            // 画像が添付されていない、または一時ファイルが見つからなかった場合
            $contact['image_file_path'] = null;
            $contact['image_file_name'] = null;
        }


        // セッションから一時的な表示用データを削除
        unset($contact['image_preview_url']);
        if (isset($contact['image_file'])) {
            unset($contact['image_file']);
        }

        // ログインしていない場合はID 1を強制的に使用
        $contact['user_id'] = Auth::id() ?? 1;

        Contact::create($contact);
        $request->session()->forget('contact');
        return redirect()->route('complete');
    }

    /**
     * 確認画面からフォームへ戻る (back)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back(Request $request): RedirectResponse
    {
        $contact = $request->session()->get('contact');

        if (!$contact) {
            return redirect()->route('index');
        }

        // Base64データや一時パスも全てold()としてフラッシュ
        // index.blade.php側で old('image_preview_url') などで復元される
        return redirect()->route('index')->withInput($contact);
    }

    /**
     * 完了画面表示 (complete)
     * @return \Illuminate\View\View
     */
    public function complete(): View
    {
        return view('contents.complete');
    }

    /**
     * お問い合わせ履歴の表示 (history)
     * @return \Illuminate\View\View
     */
    public function history(): View
    {
        // ログインユーザーに紐づく履歴のみ取得
        $userId = Auth::id() ?? 1;
        $contacts = Contact::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('contents.history', compact('contacts'));
    }


    /**
     * 投稿編集フォームを表示 (edit)
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id): View|RedirectResponse
    {
        // Contact インスタンスを取得
        $contact = Contact::findOrFail($id);
        $user = Auth::user();

        // 投稿者以外は編集不可
        if (!$user || $contact->user_id !== $user->id) {
             // ログインしていない、またはIDが一致しない
            return redirect()->route('index')->with('error', 'この投稿を編集する権限がありません。');
        }

        // Bladeファイルに $contact データを渡して表示
        return view('contents.edit', compact('contact'));
    }

    /**
     * 投稿を更新 (update)
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // 1. Contactモデルのインスタンスを取得
        $contact = Contact::findOrFail($id);
        $user = Auth::user();

        // 権限チェック
        if (!$user || $contact->user_id !== $user->id) {
            return redirect()->route('index')->with('error', 'この投稿を更新する権限がありません。');
        }

        // 2. バリデーション
        $validatedData = $request->validate([
            // name は readonly のため変更を許可しないが、念のため存在を確認
            'name' => 'required|string|max:255',

            // 💡 修正ポイント 1: `current_image_path`をvalidationに追加 💡
            'current_image_path' => 'nullable|string',

            // 💡 修正ポイント 2: ユニーク制約を削除（更新時は不要と判断） 💡
            'email' => [
                'required',
                'email',
                'max:255',
                // Rule::unique('contacts', 'email')->ignore($contact->id, 'id'),  <-- これを削除
            ],

            'tel' => 'nullable|string|max:20',
            'content' => 'required|string',
            'image_file' => 'nullable|image|max:2048',

            // delete_image は Blade から送信される隠しフィールド
            'delete_image' => 'nullable|in:0,1',
        ]);

        // 3. 画像ファイルの処理の初期値設定
        // 💡 修正 3: 初期値として hidden field で送られてきたパスを使用する 💡
        // 新しいファイルアップロードも削除要求もない場合、このパスが維持される
        $newImagePath = $validatedData['current_image_path'];
        $newImageName = $contact->image_file_name; // 名前は元のものを維持する前提

        // A. 既存画像の削除処理 (ユーザーが「画像を削除」ボタンを押した場合)
        if ($request->input('delete_image') === '1') {
            // 既存のファイルをストレージから削除
            if ($newImagePath && Storage::exists('public/' . $newImagePath)) {
                Storage::delete('public/' . $newImagePath);
            }
            $newImagePath = null;
            $newImageName = null;
        }

        // B. 新しい画像ファイルがアップロードされた場合の処理 (delete_image=1でも上書き)
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');

            // 既存の画像があれば削除 (新規画像で上書きされるため)
            if ($newImagePath && Storage::exists('public/' . $newImagePath)) {
                Storage::delete('public/' . $newImagePath);
            }

            // 新規ファイルを保存 (保存先は public/contacts/ に統一)
            $pathWithPublic = Storage::putFile('public/contacts', $file);

            // DBに保存するのは 'public/' を除いたパス
            $dbSavePath = str_replace('public/', '', $pathWithPublic);

            $newImagePath = $dbSavePath;
            $newImageName = $file->getClientOriginalName();
        }


        // 4. データ更新: バリデーション済みデータを使用
        $contact->update([
            'email' => $validatedData['email'],
            'tel' => $validatedData['tel'] ?? null,
            'content' => $validatedData['content'],
            'image_file_path' => $newImagePath,
            'image_file_name' => $newImageName,
        ]);


        // 5. 完了メッセージとともに一覧にリダイレクト
        return redirect()->route('contact.history')->with('success', '投稿が正常に更新されました。');
    }

    /**
     * 投稿をデータベースから削除 (destroy)
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        // 1. Contact インスタンスを取得
        $contact = Contact::findOrFail($id);
        $user = Auth::user();

        // 2. 認証チェック：投稿者が現在の認証ユーザーでなければリダイレクト
        if (!$user || $contact->user_id !== $user->id) {
            return back()->with('error', '他のユーザーの投稿は削除できません。');
        }

        // 3. 画像ファイルも削除（オプション）
        if ($contact->image_file_path && Storage::exists('public/' . $contact->image_file_path)) {
            Storage::delete('public/' . $contact->image_file_path);
        }

        // 4. 投稿を削除 (SoftDeletesを使用している場合、論理削除となる)
        $contact->delete();

        // 5. 成功メッセージと共に履歴画面へリダイレクト
        return redirect()->route('contact.history')->with('success', 'お問い合わせ履歴を削除しました。');
    }
}
