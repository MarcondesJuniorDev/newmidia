<?php

namespace App\Livewire;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Livewire\Component;

class ContactForm extends SimplePage
{
    public static string $view = 'livewire.contact-form';
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-mail')
                    ->required()
                    ->email()
                    ->maxLength(255),

                Select::make('subject')
                    ->label('Assunto')
                    ->options([
                        'general' => 'Geral',
                        'support' => 'Suporte',
                        'feedback' => 'Feedback',
                    ])
                    ->required()
                    ->default('general')
                    ->searchable()
                    ->placeholder('Selecione um assunto'),

                Textarea::make('message')
                    ->label('Mensagem')
                    ->required()
                    ->rows(5),

                Actions::make([
                    Actions\Action::make('submit')
                        ->submit('send')
                        ->label('Enviar'),

                    Actions\Action::make('back')
                        ->label('Voltar')
                        ->link()
                        ->url(filament()->getLoginUrl()),
                ])
                    ->fullWidth()
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        Notification::make()
            ->title('Mensagem enviada com sucesso!')
            ->body('Obrigado por entrar em contato conosco. Responderemos o mais breve possível.')
            ->success()
            ->send();

        $this->form->fill();
    }
}
