<form action="/admin/products/create" method="post" enctype="multipart/form-data">
    <div>
        <label>Title</label>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Price</label>
        <input type="number" name="price" required>
    </div>

    <div>
        <label>Cover image</label>
        <input type="file" name="cover_image" accept="image/*">
    </div>
    
    <button type="submit">Create</button>
</form>
