<?php

namespace App\Http\Requests;

use App\Http\Exceptions\GiphyException;
use App\Services\GiphyService;
use Illuminate\Foundation\Http\FormRequest;

class CreateFavoriteGifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gif_id' => ['required', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (): void {
                $this->validateGifId();
            },
            function (): void {
                $this->validateIsNotAlreadyFavorited();
            },
        ];
    }

    private function validateGifId(): void
    {
        try {
            app(GiphyService::class)->getGifById($this->input('gif_id'));
        } catch (GiphyException $e) {
            $this->validator->errors()->add('gif_id', $e->getMessage());
        }
    }

    private function validateIsNotAlreadyFavorited(): void
    {
        if ($this->user()->favorites()->where('gif_id', $this->input('gif_id'))->exists()) {
            $this->validator->errors()->add('gif_id', __('Already favorited'));
        }
    }
}
