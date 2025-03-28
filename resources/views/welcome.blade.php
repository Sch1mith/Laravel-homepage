<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="dse/app.css">
    <script src="/app.js"></script>
    <title>Items App</title>
    <style>
        :root {
            --dark: #333333;
            --light: #eeeeee;
        }

        body {
            font-family: Helevetica, sans-serif;
            text-align: center;
            color: var(--dark);
            font-size: 14px;
        }

        h1 {
            background-color: var(--light);
            font-family: Georgia, serif;
            width: 60%;
            margin-left: 20%;
            padding: 10px 1px;
        }

        /* Table styling */

        #table-wrapper {
            height: 220px;
            width: 50%;
            margin-left: 25%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            overflow-y: auto;
        }

        table {
            width: 100%;
        }

        th {
            position: sticky;
            top: 0;
            padding: 10px;
            background: var(--light);
            ;
        }

        td {
            padding: 7px;
        }

        .t-name {
            text-align: left;
        }

        .t-qty {
            width: 20%;
        }

        .t-radio {
            width: 15%;
        }

        .r-select {
            color: blue;
            font-weight: bolder;
            font-size: 16px;
        }

        .r-unselect {
            color: var(--dark);
            font-weight: normal;
            font-size: 14px;
        }

        /* Inputs and labels styling */

        .buttons {
            padding: 8px 14px;
            font-weight: bold;
            color: var(--dark);
            background-color: var(--light);
            ;
            border-radius: 4px;
            border-style: none;
            cursor: pointer;
        }

        .textinput {
            padding: 5px;
            color: var(--dark);
            border: 1px solid lightgrey;
        }

        .labels {
            font-size: 16px;
            font-weight: bold;
            margin-right: 5px;
        }

        #status-wrapper {
            margin-left: 25%;
            text-align: left;
        }
    </style>
</head>

<body>

    <h1>Items App</h1>

    <br>

    <div>
        <label for="name" class="labels">Name: </label>
        <input type="text" id="name" class="textinput" placeholder="Enter name (required)">
        <span style="margin-left: 20px;"></span>
        <label for="quantity" class="labels">Quantity: </label>
        <input type="number" id="quantity" class="textinput" placeholder="Enter quantity (required)">
    </div>

    <br>

    <div id="table-wrapper">
        <table id="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="t-qty">Quantity</th>
                    <th class="t-radio">Select</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <br><br>

    <div>
        <button type="button" class="buttons" onclick="addData()" title="Add new row from the text inputs">Add</button>
        <button type="button" class="buttons" onclick="updateData()" title="Update selected row">Update</button>
        <button type="button" class="buttons" onclick="deleteData()" title="Delete selected row">Delete</button>
        <span style="margin-left: 10px;"></span>
        <button type="button" class="buttons" onclick="clearData()" title="Clear selection">Clear</button>
        <button type="button" class="buttons" onclick="selectFirstOrLastRow(1)"
            title="Select first row of table">First</button>
        <button type="button" class="buttons" onclick="selectFirstOrLastRow()"
            title="Select last row of table">Last</button>
    </div>

    <br><br>

    <div id="status-wrapper">
        <span class="labels">Status: </span>
        <span id="status"></span>
    </div>

        <script> 
/* Constants and variables */

/* Data store with initial data */
const data = [
    { name: "Paper", quantity: 20 },
    { name: "Pencils", quantity: 10 },
    { name: "Paper-clips", quantity: 50 }
];

let selectedRowIx;
let prevSelection;
let table;
let status;


/* Functions */

window.onload = function () {
    status = document.getElementById("status");
    table = document.getElementById("data-table");
    loadData();
}

/*
 * Routine to get the data and populate the HTML table, initially.
 */
function loadData() {
    data.forEach(e => createTable(e));
    status.innerHTML = "Loaded " + data.length + " items.";
    if (data.length) {
        selectRow();
        scrollToSelection();
    }
}

/* 
 * Create HTML table row for each data element.
 */
function createTable(e) {
    selectedRowIx = table.rows.length;
    const tableRow = table.insertRow(selectedRowIx);
    const cell1 = tableRow.insertCell(0);
    const cell2 = tableRow.insertCell(1);
    const cell3 = tableRow.insertCell(2);
    cell1.innerHTML = e.name;
    cell1.className = "t-name";
    cell2.innerHTML = e.quantity;
    cell2.className = "t-qty";
    cell3.innerHTML = "<input type='radio' name='select' onclick='selectRow(this)' checked>";
    cell3.className = "t-radio";
}

function selectRow(obj) {

    const row = (obj) ? obj.parentElement.parentElement : table.rows[table.rows.length - 1];
    selectedRowIx = row.rowIndex;

    if (obj) {
        status.innerHTML = "Selected row " + selectedRowIx;
    }

    setSelection(row);
}

function setSelection(row) {

    document.getElementById("name").value = row.cells.item(0).innerHTML;
    document.getElementById("quantity").value = row.cells.item(1).innerHTML;
    row.className = "r-select";

    if (prevSelection && prevSelection !== selectedRowIx) {
        table.rows[prevSelection].className = "r-unselect";
    }

    prevSelection = selectedRowIx
}

function scrollToSelection() {
    const ele = document.getElementById("table-wrapper")
    const bucketHt = ele.clientHeight
    const itemHt = ele.scrollHeight / table.rows.length
    const noItemsInBucket = parseInt(bucketHt / itemHt)
    const targetBucket = (selectedRowIx + 1) / noItemsInBucket
    const scrollPos = (bucketHt * (targetBucket - 1)) + (bucketHt / 2)
    ele.scrollTop = Math.round(scrollPos)
}

/*
 * Routine to add a new item data to the HTML table and the data store.
 */
function addData() {

    const name = document.getElementById("name").value;
    const quantity = document.getElementById("quantity").value;

    if (!name) {
        alert("Name is required!");
        document.getElementById("name").focus();
        return;
    }

    if (quantity <= 0) {
        alert("Quantity must be greater than zero!");
        document.getElementById("quantity").focus();
        return;
    }

    addToTable({ name: name, quantity: quantity });
}

function addToTable(item) {
    status.innerHTML = "New item added";
    data.push(item);
    createTable(item);
    selectRow();
    scrollToSelection();
}

/*
 * Routine to update an item quantity with a new value,
 * for a selected item. Allows only change of quantity.
 */
function updateData() {

    if (!selectedRowIx) {
        alert("Select a row to update!")
    }
    else {
        const quantity = table.rows[selectedRowIx].cells.item(1).innerHTML;
        const name = table.rows[selectedRowIx].cells.item(0).innerHTML;

        const nameInput = document.getElementById("name").value;
        const quantityInput = document.getElementById("quantity").value;

        if (name !== nameInput) {
            alert("Name cannot be changed!");
            document.getElementById("name").focus();
            return;
        }

        if (quantityInput <= 0 || quantity == quantityInput) {
            alert("Quantity is required and it must be a new value!")
            document.getElementById("quantity").focus();
            return;
        }

        updateTable(name, quantityInput)
    }
}

function updateTable(name, quantity) {
    table.rows[selectedRowIx].cells.item(1).innerHTML = quantity;
    data.splice(selectedRowIx - 1, 1, { name: name, quantity: parseInt(quantity) });
    status.innerHTML = "Item quantity updated.";
    scrollToSelection();
}

/*
 * Routine to delete a selected row from the HTML table and the data store.
 */
function deleteData() {
    if (!selectedRowIx) {
        alert("Select a row to delete!");
    }
    else {
        status.innerHTML = "Item deleted";
        data.splice(selectedRowIx - 1, 1);
        table.deleteRow(selectedRowIx);
        initValues();
    }
}

function initValues() {
    selectedRowIx = null;
    prevSelection = null;
    document.getElementById("name").value = "";
    document.getElementById("quantity").value = "";
}

/*
 * Routine to clear the selected row in the HTML table as well
 * as the input and status fields.
 */
function clearData() {
    if (selectedRowIx) {
        table.rows[selectedRowIx].cells.item(2).firstChild.checked = false;
        table.rows[selectedRowIx].className = "t-unselect";
    }

    initValues();
    status.innerHTML = "";
}

/*
 * Routine for selecting the first or the last row of the HTML table 
 * (depending upon the parameter "n" - the value 1 for selecting the
 * first row, otherwise the last one).
 */
function selectFirstOrLastRow(n) {

    if (table.rows.length < 2) {
        status.innerHTML = "No data in table!";
        return;
    }

    selectedRowIx = (n === 1) ? 1 : (table.rows.length - 1);
    const row = table.rows[selectedRowIx];
    row.cells[2].children[0].checked = true;
    setSelection(row);
    scrollToSelection();
    status.innerHTML = "Selected row " + selectedRowIx;
}
</script>

</body>

</html>