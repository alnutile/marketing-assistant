<?php

namespace App\Models;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Services\LlmServices\Requests\MessageInDto;
use App\Services\LlmServices\RoleEnum;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'status' => StatusEnum::class,
        'chat_status' => ChatStatusEnum::class,
        'product_or_service' => ProductServiceEnum::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    protected static function getForm(): array
    {
        $defaultContent = <<<'DEFAULT_CONTENT'
## Unique Selling Proposition (USP)
[What makes your product/service unique? Why should your target audience choose you over competitors?]

## Key Messages
- [Message 1]
- [Message 2]
- [Message 3]


## Success Metrics
- [Metric 1]: [Target]
- [Metric 2]: [Target]
- [Metric 3]: [Target]


## Additional Notes
[Any other important information or considerations for this campaign]


DEFAULT_CONTENT;

        return [
            Forms\Components\Section::make('Campaign')
                ->description('Manage a Campaign')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\MarkdownEditor::make('description')
                        ->helperText('A brief description of the campaignA brief description of the campaign')
                        ->default($defaultContent)
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Details')
                ->description('Settings')
                ->columns(1)
                ->schema([
                    Forms\Components\DatePicker::make('start_date'),
                    Forms\Components\DatePicker::make('end_date'),
                    Forms\Components\Select::make('users')
                        ->multiple()
                        ->preload()
                        ->relationship(name: 'users', titleAttribute: 'name'),
                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(StatusEnum::class)
                        ->default(StatusEnum::DRAFT),
                    Forms\Components\Select::make('product_or_service')
                        ->required()
                        ->options(ProductServiceEnum::class)
                        ->native(false)
                        ->default(ProductServiceEnum::ConsultingService),
                    Forms\Components\MarkdownEditor::make('target_audience')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('budget')
                        ->prefixIcon('heroicon-m-currency-dollar')
                        ->prefixIconColor('success')
                        ->helperText('Budget in USD 1000 or 2500 etc')
                        ->numeric(),
                ]),
        ];
    }

    public function getContext(): string
    {
        $campaignContext = <<<CAMPAIGN_CONTEXT
        ## Unique Selling Proposition (USP)
        {$this->name}
        ## Timeline
        {$this->start_date->format('Y-m-d')}
        {$this->end_date->format('Y-m-d')}
        ## DETAILS
        {$this->content}
        ## Product of Service
        {$this->product_or_service->value}
         ## Target Audience
        {$this->target_audience}
        ## Budget
        {$this->budget}
CAMPAIGN_CONTEXT;

        return $campaignContext;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    protected function createSystemMessageIfNeeded(string $systemPrompt): void
    {
        if ($this->messages()->count() == 0) {

            $this->messages()->create(
                [
                    'content' => $systemPrompt,
                    'role' => \App\Services\LlmServices\RoleEnum::System,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function addInput(string $message,
        RoleEnum $role = RoleEnum::User,
        ?string $systemPrompt = null,
        ?User $user = null): Message
    {

        return DB::transaction(function () use ($message, $role, $systemPrompt, $user) {

            if ($systemPrompt) {
                $this->createSystemMessageIfNeeded($systemPrompt);
            }

            return $this->messages()->create(
                [
                    'content' => $message,
                    'role' => $role,
                    'user_id' => $user?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        });

    }

    public function getMessageThread(int $limit = 10): array
    {
        $latestMessages = $this->messages()
            ->orderBy('id', 'desc')
            ->get();

        $latestMessagesArray = [];

        foreach ($latestMessages as $message) {
            /**
             * @NOTE
             * I am super verbose here due to an odd BUG
             * I keep losing the data due to some
             * magic toArray() method that
             * was not working
             */
            $asArray = [
                'role' => $message->role->value,
                'content' => $message->content,
                'tool_id' => $message->tool_id,
                'tool' => $message->tool_name,
                'args' => $message->args ?? [],
            ];

            $dto = new MessageInDto(
                content: $asArray['content'],
                role: $asArray['role'],
                tool_id: $asArray['tool_id'],
                tool: $asArray['tool'],
                args: $asArray['args'],
            );

            $latestMessagesArray[] = $dto;
        }

        return array_reverse($latestMessagesArray);

    }
}
