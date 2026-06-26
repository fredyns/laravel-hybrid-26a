# File Uploads

**Source URL:** https://livewire.laravel.com/docs/4.x/uploads

## Overview

Livewire offers powerful support for uploading files within your components.

First, add the `WithFileUploads` trait to your component. Once this trait has been added to your component, you can use `wire:model` on file inputs as if they were any other input type and Livewire will take care of the rest.

## Basic File Upload

Here's an example of a simple component that handles uploading a photo:

```php
<?php // resources/views/components/⚡upload-photo.blade.php

use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;

new class extends Component {
    use WithFileUploads;

    #[Validate('image|max:1024')] // 1MB Max
    public $photo;

    public function save()
    {
        $this->photo->store(path: 'photos');
    }
};
?>

<form wire:submit="save">
    <input type="file" wire:model="photo">

    @error('photo') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">Save photo</button>
</form>
```

**Important:** The "upload" method is reserved. Notice the above example uses a "save" method instead of an "upload" method. The term "upload" is reserved by Livewire. You cannot use it as a method or property name in your component.

From the developer's perspective, handling file inputs is no different than handling any other input type: Add `wire:model` to the `<input>` tag and everything else is taken care of for you.

However, more is happening under the hood to make file uploads work in Livewire. Here's what goes on when a user selects a file to upload:

1. When a new file is selected, Livewire's JavaScript makes an initial request to the component on the server to get a temporary "signed" upload URL.
2. Once the URL is received, JavaScript does the actual "upload" to the signed URL, storing the upload in a temporary directory designated by Livewire and returning the new temporary file's unique hash ID.
3. Once the file is uploaded and the unique hash ID is generated, Livewire's JavaScript makes a final request to the component on the server, telling it to "set" the desired public property to the new temporary file.
4. Now, the public property (in this case, `$photo`) is set to the temporary file upload and is ready to be stored or validated at any point.

## Storing Uploaded Files

The previous example demonstrates the most basic storage scenario: moving the temporarily uploaded file to the "photos" directory on the application's default filesystem disk.

However, you may want to customize the file name of the stored file or even specify a specific storage "disk" to keep the file on (such as S3).

### Common Storage Scenarios

```php
public function save()
{
    // Store the file in the "photos" directory of the default filesystem disk
    $this->photo->store(path: 'photos');

    // Store the file in the "photos" directory in a configured "s3" disk
    $this->photo->store(path: 'photos', options: 's3');

    // Store the file in the "photos" directory with the filename "avatar.png"
    $this->photo->storeAs(path: 'photos', name: 'avatar');

    // Store the file in the "photos" directory in a configured "s3" disk with the filename "avatar.png"
    $this->photo->storeAs(path: 'photos', name: 'avatar', options: 's3');

    // Store the file in the "photos" directory, with "public" visibility in a configured "s3" disk
    $this->photo->storePublicly(path: 'photos', options: 's3');

    // Store the file in the "photos" directory, with the name "avatar.png", with "public" visibility in a configured "s3" disk
    $this->photo->storePubliclyAs(path: 'photos', name: 'avatar', options: 's3');
}
```

**Tip:** You can access the original file name of a temporary upload by calling its `->getClientOriginalName()` method.

Livewire honors the same APIs Laravel uses for storing uploaded files, so feel free to consult Laravel's file upload documentation.

## Handling Multiple Files

Livewire automatically handles multiple file uploads by detecting when a file input has the `multiple` attribute.

```html
<input type="file" wire:model="photos" multiple>
```

```php
public $photos = [];

public function save()
{
    $this->validate([
        'photos.*' => 'image|max:1024',
    ]);

    foreach ($this->photos as $photo) {
        $photo->store(path: 'photos');
    }
}
```

## Real-Time File Upload Progress

Livewire provides a way to show upload progress to users using Alpine and the `wire:loading` directive:

```html
<input type="file" wire:model="photo">

<div wire:loading wire:target="photo">
    Uploading...
</div>
```

For more detailed progress information, you can use Alpine's `$wire` magic to access upload progress:

```html
<input type="file" wire:model="photo" @change="uploadProgress = 0">

<div wire:loading wire:target="photo">
    Upload progress: <span x-text="uploadProgress"></span>%
</div>
```

## Validation

File uploads can be validated just like any other property using the `#[Validate]` attribute:

```php
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;

class UploadPhoto extends Component
{
    use WithFileUploads;

    #[Validate('required|image|max:1024')]
    public $photo;

    public function save()
    {
        $this->photo->store(path: 'photos');
    }
}
```

## Cleaning Up Temporary Files

Livewire automatically cleans up temporary files after a period of time. However, if you want to manually delete a temporary file, you can do so:

```php
$this->photo->delete();
```

## See Also

- [Validation](./validation.md) — Validate file uploads
- [Forms](../essentials/forms.md) — Handle form submissions
- [Actions](../essentials/actions.md) — Trigger component methods
