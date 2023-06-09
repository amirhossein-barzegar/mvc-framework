<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>List Employees</title>
</head>
<body class="bg-gray-50">
<div class="container mx-auto bg-white px-4 py-2 rounded shadow-sm">
    <h1 class="text-2xl font-bold">List of all the employees</h1>
    <div class="grid grid-cols-12 gap-4 my-4">
        <?php foreach($employees as $employee): ?>
        <div class="col-span-3 flex flex-col p-4 border border-blue-400 rounded-lg">
            <div class="">
                <span class="font-bold">Name:</span>
                <a href="/employee/<?php echo $employee->id ?>"><?php echo $employee->name ?></a>
            </div>
            <div class="">
                <span class="font-bold">email:</span>
                <?php echo $employee->email ?>
            </div>
            <div class="">
                <span class="font-bold">Age:</span>
                <?php echo $employee->age ?>
            </div>
            <div class="mt-2 flex justify-between">
                <a href="" class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm hover:bg-red-600">Delete</a>
                <a href="" class="px-4 py-2 rounded-lg bg-blue-500 text-white text-sm hover:bg-blue-600">Edit</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>