<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thư mục</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('modules/filemanager/css/folder.css') }}">

</head>

<body>


    <button id="openPopupButton">Mở Quản Lý Thư Mục</button>

@include('FileManager::modal.folder_modal')

    <script src="{{ asset('modules/filemanager/js/folder.js') }}"></script>


</body>

</html>