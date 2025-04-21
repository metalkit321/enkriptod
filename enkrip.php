<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Enkripsi & Dekripsi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            height: 150px; /* Lebar dan tinggi textarea */
            resize: vertical; /* Membolehkan resize vertikal */
        }
        button {
            padding: 10px 20px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        label {
            margin-right: 15px;
        }
        .hasil {
            margin-top: 20px;
            position: relative;
        }
        .copy-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 12px;
            background-color: #555;
            color: white;
            border: none;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
        textarea {
            resize: none;
            height: 150px;
        }
    </style>
</head>
<body>

<div class="container">
    <form method="POST">
    <h2>ENKRIPTOD</h2>
        <label><input type="radio" name="mode" value="encrypt" checked> 🔒 Enkripsi</label>
        <label><input type="radio" name="mode" value="decrypt"> 🔓 Dekripsi</label><br>

        <textarea name="inputText" placeholder="Masukkan teks atau paragraf panjang" required></textarea><br>
        <input type="text" name="password" placeholder="Password (4 karakter)" maxlength="4" required><br>
        <button type="submit">Proses</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mode = $_POST['mode'];
        $inputText = $_POST['inputText'];
        $password = $_POST['password'];

        if (strlen($password) !== 4) {
            echo "<div class='hasil'><strong style='color:red;'>Password harus tepat 4 karakter.</strong></div>";
        } else {
            $key = (
                ord($password[0]) +
                ord($password[1]) +
                ord($password[2]) +
                ord($password[3])
            ) % 26 + 9;

            function encrypt($text, $key) {
                $left = substr($text, 0, 3);
                $right = substr($text, -3);

                $leftEncrypted = '';
                foreach (str_split($left) as $char) {
                    $leftEncrypted .= chr((int)(ord($char) + ($key * 1.6)));
                }

                $middleEncrypted = '';
                foreach (str_split($text) as $char) {
                    $middleEncrypted .= chr(ord($char) + $key);
                }

                $rightEncrypted = '';
                foreach (str_split($right) as $char) {
                    $rightEncrypted .= chr((int)(ord($char) + ($key * 1.6 * 1.6)));
                }

                return base64_encode($leftEncrypted . $middleEncrypted . $rightEncrypted);
            }

            function decrypt($encodedText, $key) {
                $decoded = base64_decode($encodedText);
                if (!$decoded || strlen($decoded) < 7) {
                    return "[Format tidak valid]";
                }

                $middleStart = 3;
                $middleLength = strlen($decoded) - 6;
                $middlePart = substr($decoded, $middleStart, $middleLength);

                $original = '';
                foreach (str_split($middlePart) as $char) {
                    $original .= chr(ord($char) - $key);
                }

                return $original;
            }

            $hasil = ($mode === 'encrypt')
                ? encrypt($inputText, $key)
                : decrypt($inputText, $key);

            echo "<div class='hasil'>";
            echo "<textarea id='hasil' readonly>$hasil</textarea>";
            echo "<button class='copy-btn' onclick=\"copyText()\">📋 Salin</button>";
            echo "</div>";
        }
    }
    ?>

</div>

<script>
function copyText() {
    var text = document.getElementById("hasil");
    text.select();
    text.setSelectionRange(0, 99999); // untuk mobile
    document.execCommand("copy");
    alert("Teks berhasil disalin!");
}
</script>

</body>
</html>
