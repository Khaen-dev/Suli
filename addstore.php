<?php 

require "required/config.php"; 

if (isset($_POST['add-store-btn'])) {
    $store_type =$_POST['store_type'] ?? '';
    if ($store_type === 'other' && !empty($_POST['other-type'])) {
        $store_type = trim($_POST['other-type']);
    }

    $stmt =$conn->prepare("INSERT INTO stores (name, type, contact_name, contact_email, tel) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssss", 
        $_POST['store_name'], 
        $store_type,$_POST['contact_name'], 
        $_POST['contact_email'],$_POST['tel']
    );
    $stmt->execute();$stmt->close();
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolt hozzáadása</title>
    <link rel="stylesheet" href="css/boti.css">
</head>
<body>
    <form method="post">
        <label for="store_name">Bolt neve *</label>
        <input type="text" name="store_name" id="store_name" required>

        <label for="store-type-search">Bolt típusa *</label>
        <div class="store-type-row">
            <div class="search-select" id="store-type-widget">
                <input type="hidden" name="store_type" id="store_type" value="default">
                <button type="button" class="search-select-toggle" id="store-type-toggle">Válassz típust</button>
                <div class="search-select-panel" id="store-type-panel">
                    <input type="text" id="store-type-search" placeholder="Keresés..." autocomplete="off">
                    <div class="search-select-options" id="store-type-options"></div>
                </div>
            </div>
            <input type="text" name="other-type" id="other-type" placeholder="Pl.: Kávézó">
        </div>

        <label for="contact_name">Kapcsolattartó neve *</label>
        <input type="text" name="contact_name" id="contact_name" required>

        <label for="contact_email">Kapcsolattartó e-mail *</label>
        <input type="text" name="contact_email" id="contact_email" required>

        <label for="tel">Telefonszám *</label>
        <input type="tel" name="tel" id="tel" required>

        <label><input type="checkbox" id="aszf" name="aszf" value="igen" required> ÁSZF elfogadása *</label>
        <label><input type="checkbox" id="ate" name="ate" value="igen" required> Adatkezelési tájékoztató elfogadása *</label>

        <input type="submit" name="add-store-btn" value="Bolt hozzáadása">
    </form>

    <script>
        const storeTypes = [
            { value: "other", label: "Egyéb" },
            { value: "Kávézó", label: "Kávézó" }
        ];

        const $ = id => document.getElementById(id);
        const normalize = text => text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

        function renderOptions(query = "") {
            const needle = normalize(query);
            $("store-type-options").innerHTML = storeTypes
                .filter(opt => !needle || normalize(opt.label).includes(needle))
                .map(opt => `<button type="button" data-value="${opt.value}" aria-selected="${opt.value === $("store_type").value}">${opt.label}</button>`)
                .join("");
        }

        $("store-type-toggle").onclick = () => {
            if ($("store-type-panel").classList.toggle("open")) {
                $("store-type-search").focus();
            }
        };

        $("store-type-search").oninput = (e) => renderOptions(e.target.value);

        $("store-type-options").onclick = (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;

            const val = btn.dataset.value;
            $("store_type").value = val;
            $("store-type-toggle").textContent = btn.textContent;
            $("store-type-panel").classList.remove("open");
            $("store-type-search").value = "";

            const isOther = val === "other";
            $("other-type").style.display = isOther ? "inline-block" : "none";
            if (!isOther) $("other-type").value = "";

            renderOptions();
        };

        document.onclick = (e) => {
            if (!$("store-type-widget").contains(e.target)) {
                $("store-type-panel").classList.remove("open");
            }
        };

        renderOptions();
    </script>
</body>
</html>