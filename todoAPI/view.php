<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>todo API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Hlavní nadpis a tlačítka pro různé typy přístupu -->
        <div class="header">
            <h1>todo API</h1>
            <div class="button-container">
                <!-- Tlačítko pro veřejný přístup -->
                <button class="button" id="publicAccess">Veřejný přístup</button>
                <!-- Tlačítko pro přístup s API tokenem -->
                <button class="button" id="tokenAccess">Přístup s API tokenem</button>
                <!-- Tlačítko pro přístup pro test usera -->
                <button class="button" id="testuserAccess">Přístup pro test usera</button>
                <!-- Tlačítko pro přístup pro admin usera -->
                <button class="button" id="adminAccess">Přístup pro admin usera</button>
            </div>
        </div>
        <!-- Tlačítko pro přepínání zobrazení -->
        <div class="toggle-container">
            <button class="button toggle-button" id="toggleDisplay">Přepnout zobrazení</button>
        </div>
        <!-- Kontejner pro zobrazení výsledků -->
        <div id="output" class="table-container"></div>
    </div>
    <!-- Připojení JavaScriptového souboru -->
    <script src="script.js"></script>
</body>
</html>
