<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CtrlSave Admin | Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/editStrat.css">

</head>
<body>
  <div class="container main-container my-5">
    <div class="text-start">
      <h4 class="page-title">Manage Saving Strategies</h4>
      <p class="subtitle">Add, edit, remove video or resources</p>
      <p class="add-content-label">Edit Content</p>
    </div>

    <div class="form-container">
      <form>
        <div class="mb-3">
          <label class="form-label">Content Type</label>
          <select class="form-select">
            <option selected disabled>Select content type</option>
            <option value="video">Video</option>
            <option value="book">Book</option>
            <option value="article">Article</option>
          </select>
        </div>

        <!-- Title -->
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" class="form-control" placeholder="Post">
        </div>

        <!-- URL -->
        <div class="mb-3">
          <label class="form-label">URL</label>
          <input type="text" class="form-control" placeholder="yt.com">
        </div>

        <!-- Add Button -->
        <button type="submit" class="btn btn-add mt-3">Save</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>