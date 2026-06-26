# Date picker - PRO

Source: https://fluxui.dev/components/date-picker

Allow users to select dates or date ranges via a calendar overlay. Perfect for filtering data or scheduling events.

## Basic Example

```html
<flux:date-picker />
```

## Basic usage

```html
<flux:date-picker value="2026-01-27" />
```

```html
<flux:date-picker wire:model="date" />
```

```php
<?php

use Illuminate\Support\Carbon;
use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public ?Carbon $date;

    public function save()
    {
        Post::create([
            // ...
            'published_at' => $this->date,
        ]);

        // ...
    }
}
```

## Input trigger

```html
<flux:date-picker wire:model="date">
    <x-slot name="trigger">
        <flux:date-picker.input />
    </x-slot>
</flux:date-picker>
```

## Range picker

```html
<flux:date-picker mode="range" />
```

```html
<flux:date-picker mode="range" value="2026-01-02/2026-01-06" />
```

```html
<flux:date-picker mode="range" wire:model="range" />
```

```html
<?php use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public array $range;

    public function mount()
    {
        $this->range = [
            'start' => now()->subMonth()->format('Y-m-d'),
            'end' => now()->format('Y-m-d'),
        ];
    }
}
```

```html
<?php use Livewire\Component;
use Flux\DateRange;

class Dashboard extends Component
{
    public DateRange $range;

    public function mount()
    {
        $this->range = new DateRange(now()->subMonth(), now());
    }
}
```

## Range limits

```html
<flux:date-picker mode="range" min-range="3" />
```

```html
<flux:date-picker mode="range" max-range="10" />
```

## Range with inputs

```html
<flux:date-picker mode="range">
    <x-slot name="trigger">
        <div class="flex flex-col sm:flex-row gap-6 sm:gap-4">
            <flux:date-picker.input label="Start" />
            <flux:date-picker.input label="End" />
        </div>
    </x-slot>
</flux:date-picker>
```

## Presets

```html
<flux:date-picker mode="range" with-presets />
```

```html
<flux:date-picker mode="range" presets="today yesterday thisWeek last7Days thisMonth yearToDate allTime" />
```

## All time

```html
<flux:date-picker mode="range" presets="... allTime" :min="auth()->user()->created_at->format('Y-m-d')" />
```

```html
use Flux\DateRange;

$this->range = DateRange::allTime(start: auth()->user()->created_at);
```

```html
$orders = Order::when($this->range->isNotAllTime(), function ($query) => {
    $query->whereBetween('created_at', $this->range);
})->get();
```

## Custom range preset

```html
<flux:date-picker mode="range" presets="... custom" />
```

## Unavailable dates

```html
<flux:date-picker unavailable="2026-01-26,2026-01-28" />
```

## With today shortcut

```html
<flux:date-picker with-today />
```

## Selectable header

```html
<flux:date-picker selectable-header />
```

## Fixed weeks

```html
<flux:date-picker fixed-weeks />
```

## Start day

```html
<flux:date-picker start-day="1" />
```

## Open to

```html
<flux:date-picker open-to="2027-02-01" />
```

```html
<flux:date-picker open-to="2027-02-01" force-open-to />
```

## Week numbers

```html
<flux:date-picker week-numbers />
```

## Localization

```html
<flux:date-picker locale="ja-JP" />
```

## The DateRange object

```html
<flux:calendar wire:model.live="range" />
```

```html
<?php use Livewire\Component;
use Flux\DateRange;

class Dashboard extends Component
{
    public DateRange $range;
}
```

## Instantiation

```html
<?php use Livewire\Component;
use Flux\DateRange;

class Dashboard extends Component
{
    public DateRange $range;

    public function mount()
    {
        $this->range = new DateRange(now(), now()->addDays(7));
    }
}
```

## Persisting to the session

```html
<?php use Livewire\Attributes\Session;
use Livewire\Component;
use Flux\DateRange;

class Dashboard extends Component
{
    #[Session]
    public DateRange $range;
}
```

## Using with Eloquent

```html
<?php use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Order;
use Flux\DateRange;

class Dashboard extends Component
{
    public ?DateRange $range;

    #[Computed]
    public function orders()
    {
        return $this->range ? Order::whereBetween('created_at', $this->range)->get() : Order::all();
    }
}
```

## Available methods

```html
$range = new Flux\DateRange(
    now()->subDays(1),
    now()->addDays(1),
);

// Get the start and end dates as Carbon instances...
$start = $range->start();
$end = $range->end();

// Check if the range contains a date...
$range->contains(now());

// Get the number of days in the range...
$range->length();

// Loop over the range by day...
foreach ($range as $date) {
    // $date is a Carbon instance...
}

// Get the range as an array of Carbon instances representing each day in the range...
$range->toArray();
```

```html
$orders = Order::whereBetween('created_at', $range)->get();
```

## Range presets

```html
[
    'start' => null,
    'end' => null,
    'preset' => 'lastMonth',
]
```

```html
<?php use Livewire\Component;

class Dashboard extends Component
{
    public array $range;

    public function mount()
    {
        $this->range = [
            'start' => null,
            'end' => null,
            'preset' => 'lastMonth',
        ];
    }
}
```

```html
<?php use Livewire\Component;
use Flux\DateRange;

class Dashboard extends Component
{
    public DateRange $range;

    public function mount()
    {
        $this->range = DateRange::lastMonth();
    }
}
```

```html
$this->range->preset(); // This will return a value like `DateRangePreset::LastMonth`...
```

## Reference

### `flux:date-picker`

| Prop | Description |
| --- | --- |
| wire:model | Binds the date picker to a Livewire property. See the wire:model documentation for more information. |
| value | Selected date(s). Format depends on mode: single date (Y-m-d) or range (Y-m-d/Y-m-d). |
| mode | Selection mode. Options: single (default), range. |
| min-range | Minimum number of days that can be selected in range mode. |
| max-range | Maximum number of days that can be selected in range mode. |
| min | Earliest selectable date. Can be a date string or "today". |
| max | Latest selectable date. Can be a date string or "today". |
| open-to | Set the date that the date picker will open to, if there is no selected date. |
| force-open-to | If true, forces the date picker to open to the open-to date regardless of the selected date. Default: false. |
| months | Number of months to display. Default: 1 in single mode, 2 in range mode. |
| label | Label text displayed above the date picker. When provided, wraps the component in a flux:field with an adjacent flux:label. |
| description | Help text displayed below the date picker. When provided alongside label, appears between the label and date picker within the flux:field wrapper. |
| description:trailing | The description provided will be displayed below the date picker instead of above it. |
| badge | Badge text displayed at the end of the flux:label component when the label prop is provided. |
| placeholder | Placeholder text displayed when no date is selected. Default depends on mode. |
| size | Size of the calendar day cells. Options: sm, default, lg, xl, 2xl. |
| start-day | The day of the week to start the calendar on. Options: 0 (Sunday) to 6 (Saturday). Default: based on the user's locale. |
| week-numbers | If true, displays week numbers in the calendar. |
| selectable-header | If true, displays month and year dropdowns for quick navigation. |
| with-today | If true, displays a button to quickly navigate to today's date. |
| with-inputs | If true, displays date inputs at the top of the calendar for manual date entry. |
| with-confirmation | If true, requires confirmation before applying the selected date(s). |
| with-presets | If true, displays preset date ranges. Use with presets to customize available options. |
| presets | Space-separated list of preset date ranges to display. Default: today yesterday thisWeek last7Days thisMonth yearToDate allTime. |
| clearable | Displays a clear button when a date is selected. |
| disabled | Prevents user interaction with the date picker. |
| invalid | Applies error styling to the date picker. |
| locale | Set the locale for the date picker. Examples: fr, en-US, ja-JP. |

| Slot | Description |
| --- | --- |
| trigger | Custom trigger element to open the date picker. Usually a `flux:date-picker.input` or `flux:date-picker.button`. |

| Attribute | Description |
| --- | --- |
| data-flux-date-picker | Applied to the root element for styling and identification. |

### `flux:date-picker.input`

| Prop | Description |
| --- | --- |
| label | Label text displayed above the input. When provided, wraps the input in a flux:field component with an adjacent flux:label component. |
| description | Help text displayed below the input. When provided alongside label, appears between the label and input within the flux:field wrapper. |
| placeholder | Placeholder text displayed when no date is selected. |
| clearable | Displays a clear button when a date is selected. |
| disabled | Prevents user interaction with the input. |
| invalid | Applies error styling to the input. |

### `flux:date-picker.button`

| Prop | Description |
| --- | --- |
| placeholder | Text displayed when no date is selected. |
| size | Size of the button. Options: sm, xs. |
| clearable | Displays a clear button when a date is selected. |
| disabled | Prevents user interaction with the button. |
| invalid | Applies error styling to the button. |

### `DateRange Object`
A specialized object for handling date ranges when using mode="range".

| Method | Description |
| --- | --- |
| $range->start() | Get the start date as a Carbon instance. |
| $range->end() | Get the end date as a Carbon instance. |
| $range->days() | Get the number of days in the range. |
| $range->preset() | Get the current preset as a DateRangePreset enum value. |
| $range->toArray() | Get the range as an array with start and end keys. |

| Static Method | Description |
| --- | --- |
| DateRange::today() | Create a DateRange for today. |
| DateRange::yesterday() | Create a DateRange for yesterday. |
| DateRange::thisWeek() | Create a DateRange for the current week. |
| DateRange::lastWeek() | Create a DateRange for the previous week. |
| DateRange::last7Days() | Create a DateRange for the last 7 days. |
| DateRange::last30Days() | Create a DateRange for the last 30 days. |
| DateRange::thisMonth() | Create a DateRange for the current month. |
| DateRange::lastMonth() | Create a DateRange for the previous month. |
| DateRange::thisQuarter() | Create a DateRange for the current quarter. |
| DateRange::lastQuarter() | Create a DateRange for the previous quarter. |
| DateRange::thisYear() | Create a DateRange for the current year. |
| DateRange::lastYear() | Create a DateRange for the previous year. |
| DateRange::yearToDate() | Create a DateRange from January 1st to today. |
| DateRange::allTime() | Create a DateRange with no limits. |
