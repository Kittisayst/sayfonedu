<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestAnnouncementsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'ປະກາດຂ່າວສານລ່າສຸດ';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $userType = $this->getUserType($user);

        return $table
            ->query(
                Announcement::query()
                    ->where(function($query) use ($userType) {
                        $query->where('target_group', 'all')
                            ->orWhere('target_group', $userType);
                    })
                    ->where(function($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now()->toDateString());
                    })
                    ->where(function($query) {
                        $query->whereNull('start_date')
                            ->orWhere('start_date', '<=', now()->toDateString());
                    })
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('ຫົວຂໍ້')
                    ->searchable()
                    ->limit(50),
                
                TextColumn::make('target_group')
                    ->label('ກຸ່ມເປົ້າໝາຍ')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'all' => 'ທັງໝົດ',
                        'teachers' => 'ຄູສອນ',
                        'students' => 'ນັກຮຽນ',
                        'parents' => 'ຜູ້ປົກຄອງ',
                        default => $state,
                    }),
                
                TextColumn::make('creator.username')
                    ->label('ຜູ້ສ້າງ')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('ວັນທີສ້າງ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('ລາຍລະອຽດ')
                    ->url(fn (Announcement $record): string => route('filament.admin.resources.announcements.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultPaginationPageOption(5);
    }
    
    /**
     * ກຳນົດປະເພດຂອງຜູ້ໃຊ້ (teachers, students, parents)
     */
    private function getUserType($user): string
    {
        if ($user->hasRole('teacher')) {
            return 'teachers';
        } elseif ($user->hasRole('student')) {
            return 'students';
        } elseif ($user->hasRole('parent')) {
            return 'parents';
        }
        
        return 'all'; // default
    }
}