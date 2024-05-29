var currentAccessType = 'public'; // Proměnná pro uchování aktuálního typu přístupu
var isJsonDisplayed = true; // Proměnná pro sledování zobrazení dat (JSON vs. tabulka)

document.addEventListener('DOMContentLoaded', function() {
    // Funkce spuštěná při načtení DOM
    // Tlačítka pro přístup
    const publicButton = document.getElementById('publicAccess');
    const tokenButton = document.getElementById('tokenAccess');
    const testuserButton = document.getElementById('testuserAccess');
    const adminButton = document.getElementById('adminAccess');

    // Tlačítko pro přepnutí zobrazení
    const toggleButton = document.getElementById('toggleDisplay');

    // Funkce pro načtení dat
    function fetchData(accessType) {
        // Vytvoření nové instance XMLHttpRequest
        var xhr = new XMLHttpRequest();
        // Nastavení funkce, která se má zavolat při změně stavu požadavku
        xhr.onreadystatechange = function() {
            // Pokud je stav připravený (readyState = 4) a status HTTP odpovědi je OK (status = 200)
            if (this.readyState == 4 && this.status == 200) {
                // Nastavení HTML obsahu elementu s id "output" na odpověď serveru (s formátováním jako předformátovaný text)
                document.getElementById("output").innerHTML = `<pre>${this.responseText}</pre>`;
                // Aktualizace proměnných pro typ přístupu a zobrazení dat
                currentAccessType = accessType;
                isJsonDisplayed = true;
                // Zobrazení tlačítka pro přepnutí zobrazení
                showToggleButton();
            }
        };
        
        // Definice URL adresy pro získání dat
        var url = "http://todo.wz.cz/controller.php?action=getData";
        // Definice HTTP metody (GET)
        var method = "GET";
        // Asynchronní požadavek (true)
        var async = true;
    
        // Otevření spojení s serverem
        xhr.open(method, url, async);
    
        // Nastavení hlavičky požadavku na základě typu přístupu
        if (accessType === 'token') {
            var token = prompt("Zadejte API token:"); // Získání tokenu od uživatele pomocí promptu
            xhr.setRequestHeader("Authorization", "Bearer " + token);
        } else if (accessType === 'testuser') {
            var password = prompt("Zadejte heslo pro testovacího uživatele:"); // Získání hesla od uživatele pomocí promptu
            xhr.setRequestHeader("Authorization", "Basic " + btoa("testuser:" + password));
        } else if (accessType === 'admin') {
            var password = prompt("Zadejte heslo pro administrátora:"); // Získání hesla od uživatele pomocí promptu
            xhr.setRequestHeader("Authorization", "Basic " + btoa("admin:" + password));
        }
    
        // Odeslání požadavku na server
        xhr.send();
    }

    // Funkce pro přepnutí zobrazení
    function toggleDisplay() {
        // Pokud jsou zobrazena data ve formátu JSON, převedeme je na tabulku, jinak načteme nová data
        if (isJsonDisplayed) {
            // Pokud jsou data ve formátu JSON, zavoláme funkci pro převedení JSON na tabulku
            convertJsonToTable();
        } else {
            // Jinak zavoláme funkci pro načtení nových dat
            fetchData(currentAccessType);
        }
    }

    // Naslouchání událostem na tlačítcích pro přístup

    // Při kliknutí na tlačítko pro veřejný přístup se zavolá funkce pro načtení dat s typem 'public'
    publicButton.addEventListener('click', function() {
        fetchData('public');
    });

    // Při kliknutí na tlačítko pro přístup s API tokenem se zavolá funkce pro načtení dat s typem 'token'
    tokenButton.addEventListener('click', function() {
        fetchData('token');
    });

    // Při kliknutí na tlačítko pro přístup pro testovacího uživatele se zavolá funkce pro načtení dat s typem 'testuser'
    testuserButton.addEventListener('click', function() {
        fetchData('testuser');
    });

    // Při kliknutí na tlačítko pro přístup pro administrátora se zavolá funkce pro načtení dat s typem 'admin'
    adminButton.addEventListener('click', function() {
        fetchData('admin');
    });

    // Naslouchání události na tlačítku pro přepnutí zobrazení
    toggleButton.addEventListener('click', toggleDisplay);
});

// Funkce pro převod dat z JSON na tabulku
function convertJsonToTable() {
    // Získání textu ve formátu JSON z elementu s id "output"
    var json = document.getElementById("output").textContent;
    try {
        // Pokus o parsování textu JSON na objekt
        var data = JSON.parse(json);
        // Vytvoření HTML kódu pro tabulku
        var table = "<table>";
        table += "<tr>";
        // Vytvoření hlavičky tabulky z klíčů prvního objektu v poli dat
        for (var key in data[0]) {
            table += "<th>" + key + "</th>";
        }
        // Přidání sloupce pro odstranění úkolů pouze pro přístup typu 'admin'
        if (currentAccessType === 'admin') {
            table += "<th>Odstranit</th>";
        }
        table += "</tr>";
        // Vytvoření řádků tabulky z dat v poli
        for (var i = 0; i < data.length; i++) {
            table += "<tr>";
            // Vytvoření buněk tabulky pro každý klíč a hodnotu v poli dat
            for (var key in data[i]) {
                if (key === 'status') {
                    if (currentAccessType === 'admin') {
                        // Pokud je klíč 'status' a přístup typu 'admin', vytvoření select elementu pro změnu stavu úkolu
                        table += "<td><select onchange='updateTaskStatus(" + data[i]['id'] + ", this.value)'>";
                        table += "<option value='todo'" + (data[i]['status'] === 'todo' ? ' selected' : '') + ">To Do</option>";
                        table += "<option value='in_progress'" + (data[i]['status'] === 'in_progress' ? ' selected' : '') + ">In Progress</option>";
                        table += "<option value='done'" + (data[i]['status'] === 'done' ? ' selected' : '') + ">Done</option>";
                        table += "</select></td>";
                    } else {
                        // Jinak vytvoření běžné buňky s hodnotou statusu
                        table += "<td>" + data[i][key] + "</td>";
                    }
                } else {
                    // Jinak vytvoření běžné buňky s hodnotou
                    table += "<td>" + data[i][key] + "</td>";
                }
            }
            if (currentAccessType === 'admin') {
                // Přidání tlačítka pro odstranění úkolu pouze pro přístup typu 'admin'
                table += "<td><button onclick='confirmDelete(" + data[i]['id'] + ", \"" + data[i]['name'] + "\")'>Odstranit</button></td>";
            }
            table += "</tr>";
        }
        table += "</table>";
        // Nahrazení obsahu elementu s id "output" vygenerovanou tabulkou
        document.getElementById("output").innerHTML = table;
        // Nastavení proměnné isJsonDisplayed na false, protože nyní je zobrazena tabulka
        isJsonDisplayed = false;
    } catch (error) {
        // Pokud dojde k chybě při parsování JSON, vypíše se chybová zpráva do konzole
        console.error("Chyba při parsování JSON:", error);
    }
}

// Funkce pro zobrazení tlačítka pro přepnutí zobrazení
function showToggleButton() {
    // Vybereme tlačítko pro přepnutí zobrazení z DOM
    var toggleButton = document.querySelector('.toggle-container');
    // Nastavíme styl zobrazení tlačítka na flex (flexibilní zobrazení)
    toggleButton.style.display = 'flex';
}

// Funkce pro potvrzení smazání úkolu
function confirmDelete(taskId, taskName) {
    // Zobrazí se dialogové okno s potvrzením smazání úkolu
    var confirmation = confirm("Určitě chcete odstranit záznam - ID: " + taskId + ", Název: " + taskName + "?");
    if (confirmation) {
        // Pokud uživatel potvrdí smazání, zavolá se funkce deleteTask() pro smazání úkolu
        deleteTask(taskId);
    }
}

// Funkce pro smazání úkolu
function deleteTask(taskId) {
    // Vytvoření nového objektu XMLHttpRequest pro komunikaci se serverem
    var xhr = new XMLHttpRequest();
    // Nastavení callback funkce, která se zavolá po obdržení odpovědi ze serveru
    xhr.onreadystatechange = function() {
        // Pokud je stav připravenosti 4 a stav HTTP odpovědi je 200 (OK), provede se následující
        if (this.readyState == 4 && this.status == 200) {
            // Zobrazí se odpověď serveru v elementu s id "output"
            document.getElementById("output").innerHTML = `<pre>${this.responseText}</pre>`;
            // Aktualizují se data, aby se zobrazilo, že úkol byl odstraněn
            fetchData(currentAccessType); // Aktualizovat data po odstranění
        }
    };
    
    // URL adresa pro smazání úkolu
    var url = "http://todo.wz.cz/controller.php?action=deleteTask&task_id=" + taskId;
    // Otevření spojení s metodou DELETE
    xhr.open("DELETE", url, true);
    // Nastavení hlavičky s autorizačními údaji pro přístup k API
    xhr.setRequestHeader("Authorization", "Basic " + btoa("admin:admin"));
    // Odeslání požadavku na server
    xhr.send();
}

// Funkce pro aktualizaci statusu úkolu
function updateTaskStatus(taskId, status) {
    // Vytvoření nového objektu XMLHttpRequest pro komunikaci se serverem
    var xhr = new XMLHttpRequest();
    // Nastavení callback funkce, která se zavolá po obdržení odpovědi ze serveru
    xhr.onreadystatechange = function() {
        // Pokud je stav připravenosti 4 a stav HTTP odpovědi je 200 (OK), provede se následující
        if (this.readyState == 4 && this.status == 200) {
            // V konzoli se vypíše zpráva potvrzující úspěšnou aktualizaci statusu
            console.log("Status updated successfully");
        }
    };
    
    // URL adresa pro aktualizaci statusu úkolu
    var url = "http://todo.wz.cz/controller.php?action=updateTaskStatus&task_id=" + taskId + "&status=" + status;
    // Otevření spojení s metodou PUT
    xhr.open("PUT", url, true);
    // Nastavení hlavičky s autorizačními údaji pro přístup k API
    xhr.setRequestHeader("Authorization", "Basic " + btoa("admin:admin"));
    // Odeslání požadavku na server
    xhr.send();
}