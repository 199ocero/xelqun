<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;

/**
 * Files upload to a temporary endpoint as soon as they are picked. The form
 * state only holds a short token, and the file is moved into place on submit.
 */
class FileUploadsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Profile')->columns(2)->schema([
                FileUpload::make('avatar')
                    ->avatar()
                    ->maxSize(2048)
                    ->directory('examples/avatars')
                    ->hint('Images up to 2 MB.'),
                FileUpload::make('resume')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120)
                    ->directory('examples/resumes')
                    ->hint('PDF only, up to 5 MB.')
                    ->required(),
            ]),

            Section::make('Image tools')->schema([
                FileUpload::make('banner')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions([null, '16:9', '1:1'])
                    ->automaticallyResizeImagesMode('contain')
                    ->automaticallyResizeImagesToWidth(1200)
                    ->automaticallyUpscaleImagesWhenResizing(false)
                    ->directory('examples/banners')
                    ->hint('Shrunk to 1200 pixels wide in the browser. Use the pencil to crop it.'),
            ]),

            Section::make('Gallery')->schema([
                FileUpload::make('gallery')
                    ->hiddenLabel()
                    ->multiple()
                    ->maxFiles(5)
                    ->reorderable()
                    ->directory('examples/gallery')
                    ->hint('Pick up to five. They upload one at a time and keep their order.'),
            ]),

            Repeater::make('documents')
                ->itemLabel('Document')
                ->defaultItems(1)
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required(),
                    FileUpload::make('file')->maxSize(4096)->directory('examples/documents')->required(),
                ]),
        ];
    }
}
