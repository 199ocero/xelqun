---
title: File upload
description: FileUpload sends files as soon as they are picked, with image editing, multiple files, relationships and limits.
---

```php
use Xelqun\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('public')
    ->directory('attachments')
    ->acceptedFileTypes(['application/pdf'])
    ->maxSize(2048);
```

<!-- example: form-file-upload -->

A file uploads as soon as it's picked. Until the form is saved, the state only holds a short token, and the file sits on a temporary disk.

## Images

`image()` only takes images. `previewable()` shows thumbnails, and `avatar()` shows one image as a compact circle.

```php
FileUpload::make('photo')->image()->avatar();
```

## Several files

```php
FileUpload::make('gallery')
    ->image()
    ->multiple()
    ->reorderable()
    ->minFiles(1)
    ->maxFiles(6)
    ->maxParallelUploads(2);
```

The state is a list, so a model needs an array cast. `appendFiles(false)` puts new files first. `maxParallelUploads()` defaults to 1.

## Size limits

`maxSize()` and `minSize()` are in kilobytes.

## Editing images

`imageEditor()` lets people crop, rotate and flip an image in the browser before saving.

```php
FileUpload::make('banner')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatioOptions(['16:9', '4:3', '1:1']);
```

`imageEditorEmptyFillColor()` sets what fills the corners of a rotated image.

## Resizing and cropping

These run in the browser, before the upload.

```php
FileUpload::make('banner')
    ->image()
    ->automaticallyCropImagesToAspectRatio('16:9')
    ->automaticallyResizeImagesToWidth(1600)
    ->automaticallyResizeImagesToHeight(900);
```

`automaticallyResizeImagesMode()` and `automaticallyUpscaleImagesWhenResizing()` change how it fits.

## Stored files

| Method                 | What it does                                                       |
| ---------------------- | ------------------------------------------------------------------ |
| `downloadable()`       | Adds a button that downloads a stored file.                        |
| `openable()`           | Adds a button that opens a stored file in a new tab.               |
| `deletable(false)`     | Stops people from removing stored files.                           |
| `deleteRemovedFiles()` | Deletes a stored file from its disk once a save no longer uses it. |
| `pasteable(false)`     | Stops taking pasted files, which is on by default.                 |
| `visibility()`         | Sets the file's visibility on the disk.                            |
| `storeFiles(false)`    | Leaves files on the temporary disk, for you to handle.             |

## A row for each file

`relationship()` keeps each file as a row of a `HasMany` or `MorphMany`.

```php
FileUpload::make('photos')
    ->multiple()
    ->relationship('photos', pathColumn: 'path', orderColumn: 'sort');
```

Rows of files that stay are kept, so anything else on them survives.

Read [uploads and security](/forms/uploads-security) before you open a form to the public.
