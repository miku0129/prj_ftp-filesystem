<div
  class="offcanvas offcanvas-start"
  tabindex="-1"
  id="offcanvasMenu"
  aria-labelledby="offcanvasMenuLabel"
>
  <div class="offcanvas-header">
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>
  <div class="offcanvas-body">

    <div>
      <form
        class="mb-5"
        action="/upload.php"
        id="upload_form"
        method="post"
        enctype="multipart/form-data"
      >
          <div class="flex flex-row gap-2">
            <div class="place-self-center">
              <i class="fa-solid fa-file"></i>
            </div>
            <span class="text-lg text-blue-500 inline-block align-middle"
              >Upload a new file</span
            >
          </div>

          <input
            class="text-sm text-stone-500 mt-3 file:mr-3 file:py-1 file:px-3 file:border-[1px] file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700 block"
            type="file"
            id="file"
            name="file"
          />

          <input
            class="text-sm text-stone-500 mt-3 py-1 px-3 border-[1px] hover:cursor-pointer hover:bg-blue-50 hover:text-blue-700 block"
            type="submit"
            name="upload_submit_file"
            value="Upload"
          />
        </div>
        <div class="mt-10">
          <div class="flex flex-row gap-2">
            <div class="place-self-center">
              <i class="fa-solid fa-folder"></i>
            </div>
            <span class="text-lg text-blue-500 inline-block align-middle"
              >Upload a new folder</span
            >
          </div>

          <input
            class="text-sm text-stone-500 mt-3 file:mr-3 file:py-1 file:px-3 file:border-[1px] file:bg-stone-50 file:text-stone-700 hover:file:cursor-pointer hover:file:bg-blue-50 hover:file:text-blue-700 block"
            type="file"
            id="file"
            name="files[]"
            webkitdirectory
            multiple
          />

          <input
            class="text-sm text-stone-500 mt-3 py-1 px-3 border-[1px] hover:cursor-pointer hover:bg-blue-50 hover:text-blue-700 block"
            type="submit"
            name="upload_submit_folder"
            value="Upload"
          />
        </div>
      </form>

  </div>
</div>
