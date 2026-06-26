# wire:stream

**Source URL:** https://livewire.laravel.com/docs/4.x/wire-stream

## Overview

Livewire allows you to stream content to a web page before a request is complete via the `wire:stream` API. This is an extremely useful feature for things like AI chat-bots which stream responses as they are generated.

**Note:** Livewire currently does not support using `wire:stream` with Laravel Octane.

## Basic Usage

To demonstrate the most basic functionality of `wire:stream`, below is a simple CountDown component that when a button is pressed displays a count-down to the user from "3" to "0":

```php
use Livewire\Component;

class CountDown extends Component
{
    public $start = 3;

    public function begin()
    {
        while ($this->start >= 0) {
            // Stream the current count to the browser...
            $this->stream(
                to: 'count',
                content: $this->start,
                replace: true,
            );

            // Pause for 1 second between numbers...
            sleep(1);

            // Decrement the counter...
            $this->start = $this->start - 1;
        };
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <button wire:click="begin">Start count-down</button>

            <h1>Count: <span wire:stream="count">{{ $start }}</span></h1>
        </div>
        HTML;
    }
}
```

Here's what's happening from the user's perspective when they press "Start count-down":

1. "Count: 3" is shown on the page
2. They press the "Start count-down" button
3. One second elapses and "Count: 2" is shown
4. This process continues until "Count: 0" is shown

All of the above happens while a single network request is out to the server.

## Streaming Chat-Bot Responses

A common use-case for `wire:stream` is streaming chat-bot responses as they are received from an API that supports streamed responses (like OpenAI's ChatGPT).

Below is an example of using `wire:stream` to accomplish a ChatGPT-like interface:

```php
use Livewire\Component;

class ChatBot extends Component
{
    public $prompt = '';

    public $question = '';

    public $answer = '';

    function submitPrompt()
    {
        $this->question = $this->prompt;

        $this->prompt = '';

        $this->js('$wire.ask()');
    }

    function ask()
    {
        $this->answer = OpenAI::ask($this->question, function ($partial) {
            $this->stream(to: 'answer', content: $partial);
        });
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <section>
                <div>ChatBot</div>

                @if ($question)
                    <article>
                        <hgroup>
                            <h3>User</h3>
                            <p>{{ $question }}</p>
                        </hgroup>

                        <hgroup>
                            <h3>ChatBot</h3>
                            <p wire:stream="answer">{{ $answer }}</p>
                        </hgroup>
                    </article>
                @endif
            </section>

            <form wire:submit="submitPrompt">
                <input wire:model="prompt" type="text" placeholder="Send a message" autofocus>
            </form>
        </div>
        HTML;
    }
}
```

Here's what's going on in the above example:

1. A user types into a text field labeled "Send a message" to ask the chat-bot a question
2. They press the [Enter] key
3. A network request is sent to the server, sets the message to the `$question` property, and clears the `$prompt` property
4. The response is sent back to the browser and the input is cleared. Because `$this->js('...')` was called, a new request is triggered to the server calling the `ask()` method
5. The `ask()` method calls on the ChatBot API and receives streamed response partials via the `$partial` parameter in the callback
6. Each `$partial` gets streamed to the browser into the `wire:stream="answer"` element on the page, showing the answer progressively reveal itself to the user
7. When the entire response is received, the Livewire request finishes and the user receives the full response

## Replace vs. Append

When streaming content to an element using `$this->stream()`, you can tell Livewire to either replace the contents of the target element with the streamed contents or append them to the existing contents.

Replacing or appending can both be desirable depending on the scenario. For example, when streaming a response from a chatbot, typically appending is desired (and is therefore the default). However, when showing something like a count-down, replacing is more fitting.

You can configure either by passing the `replace:` parameter to `$this->stream()` with a boolean value:

```php
// Append contents...
$this->stream(to: 'target', content: '...');

// Replace contents...
$this->stream(to: 'target', content: '...', replace: true);
```

Append/replace can also be specified at the target element level by appending or removing the `.replace` modifier:

```html
<!-- Append contents... -->
<div wire:stream="target">

<!-- Replace contents... -->
<div wire:stream.replace="target">
```

## Reference

**Syntax:** `wire:stream="name"`

## Modifiers

| Modifier | Description |
|----------|-------------|
| `.replace` | Replace the element's contents instead of appending |

## See Also

- [Actions](../essentials/actions.md) — Handle component methods
- [wire:ref](./wire-ref.md) — Reference specific elements
- [Components](../essentials/components.md) — Learn about Livewire components
