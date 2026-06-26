# Calendar - PRO

Source: https://fluxui.dev/components/calendar

A flexible calendar component for date selection. Supports single dates, multiple dates, and date ranges. Perfect for scheduling and booking systems.

## Basic Example

```html
<flux:calendar />
```

## Basic Usage

```html
<flux:calendar value="2026-01-27" />
```

```html
<flux:calendar wire:model="date" />
```

```html
<?php use Illuminate\Support\Carbon;
use Livewire\Component;

class BookAppointment extends Component
{
    public Carbon $date;

    public function mount()
    {
        $this->date = now();
    }
}
```

## Multiple dates

```html
<flux:calendar multiple />
```

```html
<flux:calendar multiple value="2026-01-02,2026-01-05,2026-01-15" />
```

```html
<flux:calendar multiple wire:model="dates" />
```

```html
<?php use Illuminate\Support\Carbon;
use Livewire\Component;

class RequestTimeOff extends Component
{
    public array $dates = [];

    public function mount()
    {
        $this->dates = [
            now()->format('Y-m-d'),
            now()->addDays(1)->format('Y-m-d'),
        ];
    }
}
```

## Date range

```html
<flux:calendar mode="range" />
```

```html
<flux:calendar mode="range" value="2026-01-02/2026-01-06" />
```

```html
<flux:calendar mode="range" wire:model="range" />
```

```html
<?php use Livewire\Component;

class BookFlight extends Component
{
    public ?array $range;

    public function book()
    {
        // ...
        $flight->depart_on = $this->range['start'];
        $flight->return_on = $this->range['end'];
        // ...
    }
}
```

```html
<?php use Livewire\Component;
use Flux\DateRange;

class BookFlight extends Component
{
    public ?DateRange $range;

    public function book()
    {
        // ...
        $flight->depart_on = $this->range->start();
        $flight->return_on = $this->range->end();
        // ...
    }
}
```

## Range Configuration

```html
<!-- Set minimum and maximum range limits -->
<flux:calendar mode="range" min-range="3" max-range="10" />

<!-- Control number of months shown -->
<flux:calendar mode="range" months="2" />
```

## Size

```html
<flux:calendar size="xl" />
```

## Static

```html
<flux:calendar static value="2026-01-27" size="xs" :navigation="false" />
```

## Min/max dates

```html
<flux:calendar max="2026-01-27" />
```

```html
<!-- Prevent selection before today... -->
<flux:calendar min="today" />

<!-- Prevent selection after today... -->
<flux:calendar max="today" />
```

## Unavailable dates

```html
<flux:calendar unavailable="2026-01-26,2026-01-28" />
```

## With today shortcut

```html
<flux:calendar with-today />
```

## Selectable header

```html
<flux:calendar selectable-header />
```

## Fixed weeks

```html
<flux:calendar fixed-weeks />
```

## Start day

```html
<flux:calendar start-day="1" />
```

## Open to

```html
<flux:calendar open-to="2027-02-01" />
```

```html
<flux:calendar open-to="2027-02-01" force-open-to />
```

## Week numbers

```html
<flux:calendar week-numbers />
```

## Localization

```html
<flux:calendar locale="ja-JP" />
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

## Reference

### `flux:calendar`

| Prop | Description |
| --- | --- |
| wire:model | Binds the calendar to a Livewire property. See the wire:model documentation for more information. |
| value | Selected date(s). Format depends on mode: single date (Y-m-d), multiple dates (comma-separated Y-m-d), or range (Y-m-d/Y-m-d). |
| mode | Selection mode. Options: single (default), multiple, range. |
| min | Earliest selectable date. Can be a date string or "today". |
| max | Latest selectable date. Can be a date string or "today". |
| size | Calendar size. Options: base (default), xs, sm, lg, xl, 2xl. |
| start-day | The day of the week to start the calendar on. Options: 0 (Sunday) to 6 (Saturday). Default: based on the user's locale. |
| months | Number of months to display. Default: 1 for single/multiple modes, 2 for range mode. |
| min-range | Minimum number of days that can be selected in range mode. |
| max-range | Maximum number of days that can be selected in range mode. |
| open-to | Set the date that the calendar will open to, if there is no selected date. |
| force-open-to | If true, forces the calendar to open to the open-to date regardless of the selected date. Default: false. |
| navigation | If false, hides month navigation controls. Default: true. |
| static | If true, makes the calendar non-interactive (display-only). Default: false. |
| multiple | If true, enables multiple date selection mode. Default: false. |
| week-numbers | If true, displays week numbers in the calendar. Default: false. |
| selectable-header | If true, displays month and year dropdowns for quick navigation. Default: false. |
| with-today | If true, displays a button to quickly navigate to today's date. Default: false. |
| with-inputs | If true, displays date inputs at the top of the calendar for manual date entry. Default: false. |
| locale | Set the locale for the calendar. Examples: fr, en-US, ja-JP. |

### Attribute

| Attribute | Description |
| --- | --- |
| data-flux-calendar | Applied to the root element for styling and identification. |

### `DateRange Object`

| Method | Description |
| --- | --- |
| $range->start() | Get the start date as a Carbon instance. |
| $range->end() | Get the end date as a Carbon instance. |
| $range->days() | Get the number of days in the range. |
| $range->contains(date) | Check if the range contains a specific date. |
| $range->length() | Get the length of the range in days. |
| $range->toArray() | Get the range as an array with start and end keys. |
| $range->preset() | Get the current preset as a DateRangePreset enum value, if any. |

### Static Method

| Static Method | Description |
| --- | --- |
| DateRange::today() | Create a DateRange for today. |
| DateRange::yesterday() | Create a DateRange for yesterday. |
| DateRange::thisWeek() | Create a DateRange for the current week. |
| DateRange::lastWeek() | Create a DateRange for the previous week. |
| DateRange::last7Days() | Create a DateRange for the last 7 days. |
| DateRange::thisMonth() | Create a DateRange for the current month. |
| DateRange::lastMonth() | Create a DateRange for the previous month. |
| DateRange::thisYear() | Create a DateRange for the current year. |
| DateRange::lastYear() | Create a DateRange for the previous year. |
| DateRange::yearToDate() | Create a DateRange from January 1st to today. |
