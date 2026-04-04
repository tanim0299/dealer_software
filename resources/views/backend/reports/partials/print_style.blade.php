<style>
body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #fff;
    color: #000;
}

.container {
    width: 100%;
    padding: 20px 50px;
    box-sizing: border-box;
}

.info {
    margin-top: 12px;
    margin-bottom: 8px;
    font-size: 12px;
}

.summary-box {
    margin-top: 8px;
    margin-bottom: 10px;
    font-size: 12px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}

.summary-item {
    border: 1px solid #000;
    padding: 8px;
}

.summary-item strong {
    display: block;
    margin-top: 4px;
    font-size: 14px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 12px;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
}

th {
    text-align: left;
    font-weight: bold;
}

.text-end {
    text-align: right;
}

.footer {
    margin-top: 18px;
    font-size: 11px;
}

@media print {
    .print-btn {
        display: none;
    }
}
</style>

