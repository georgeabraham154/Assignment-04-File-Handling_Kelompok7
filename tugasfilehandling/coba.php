
<?php
    if (isset($_POST['submit'])) {
        $originalTag = trim($_POST['original_tag']);
        $newTag = trim($_POST['new_tag']);
        $outputType = $_POST['output_type'];

        if (isset($_FILES['html_file']) && $_FILES['html_file']['error'] === 0) {
            $uploadedFile = $_FILES['html_file']['tmp_name'];
            $originalName = $_FILES['html_file']['name'];

            // buat Baca isi file pakai fread
            $handle = fopen($uploadedFile, "r");
            $size = filesize($uploadedFile);
            $htmlContent = fread($handle, $size);
            fclose($handle);

            // buat variabel  Ubah tag HTML pakai regex
            $patternOpenTag = "/<\s*{$originalTag}(\s[^>]*)?>/i";
            $patternCloseTag = "/<\s*\/\s*{$originalTag}\s*>/i";
            $replacementOpenTag = "<$newTag$1>";
            $replacementCloseTag = "</$newTag>";

            $modifiedContent = preg_replace($patternOpenTag, $replacementOpenTag, $htmlContent);
            $modifiedContent = preg_replace($patternCloseTag, $replacementCloseTag, $modifiedContent);

            // nentuin path atau lokasi directory/folder penyimpanan hasil
            if (!file_exists("uploads")) {
                mkdir("uploads", 0777, true);
            }

            if ($outputType === "O") {
                $savePath = "uploads/" . $originalName;
                $displayName = basename($savePath) . " (overwrite)";
            } else {
                $savePath = "uploads/" . pathinfo($originalName, PATHINFO_FILENAME) . "-new.html";
                $displayName = basename($savePath);
            }

            // Simpan hasil ubahan ke file baru
            $saveHandle = fopen($savePath, "w");
            fwrite($saveHandle, $modifiedContent);
            fclose($saveHandle);

            echo "<p><strong>Berhasil!</strong> File disimpan sebagai: <a href='$savePath' target='_blank'>$displayName</a></p>";
        } else {
            echo "<p style='color:red;'>Terjadi kesalahan saat upload file.</p>";
        }
    }
    ?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Tag HTML</title>
</head>
<body>
    <h2>Form Ubah Tag HTML</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Upload file HTML:</label><br>
        <input type="file" name="html_file" accept=".html" required><br><br>

        <label>Tag asal </label><br>
        <input type="text" name="original_tag" required><br><br>

        <label>Tag baru </label><br>
        <input type="text" name="new_tag" required><br><br>

        <label>Tipe keluaran:</label><br>
        <select name="output_type" required>
            <option value="O">Overwrite </option>
            <option value="N">New File </option>
        </select><br><br>

        <button type="submit" name="submit">submit</button>
    </form>
</body>
</html>
