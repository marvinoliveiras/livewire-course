<?php
namespace App\Http\Livewire\User;
use Livewire\{Component,
    WithFileUploads};
use Illuminate\Support\Str;
class UploadPhoto extends Component
{
    use WithFileUploads;
    public $photo;
    public function render()
    {
        return view(
            'livewire.user.upload-photo'
        );
    }
    public function storagePhoto()
    {
        $this->validate([
            'photo' =>
                'required|image|max:3072'
        ]);
        $user = auth()->user();
        $path = $this->uploadPhoto(
            $user
        );
        if($path){
            $this->updateDatabase($user,
                $path
            );
            return redirect()
                ->route('tweets.index');
        }
    }
    protected function uploadPhoto($user)
    {
        $this->deleteOldPhoto($user
            ->profile_photo_path
        );
        $nameFile = Str::slug($user->name)
            .'.'.$this->photo
            ->getClientOriginalExtension();
        return $this->photo
            ->storeAs('users',
                $nameFile
            );
    }
    protected function updateDatabase(
        $user, $path)
    {
        $user->profile_photo_path = $path;
        $user->update();
    }
    protected function deleteOldPhoto($oldPhoto)
    {
        $completePathOldPhoto = __DIR__
            .'/../../../../storage/app/public/'
            .$oldPhoto;
        if($oldPhoto != null && is_file(
            $completePathOldPhoto)
        ){
            unlink($completePathOldPhoto);
        }
    }
}