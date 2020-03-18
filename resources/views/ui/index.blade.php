@extends('voyager::app')

@section('page-title', 'UI')

@section('content')
<div class="card">
    <h2 class="title">Buttons</h2>
    <div class="body">
        <div class="card">
            <h3 class="title text-lg">Primary Accent</h3>
            <div class="body">
                <button class="button bg-primary hover-bg-primary">Accent</button>
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Normal</h3>
            <div class="body">
                <button class="button red">Red</button>
                <button class="button green">Green</button>
                <button class="button blue">Blue</button>
                <button class="button yellow">Yellow</button>
                <button class="button purple">Purple</button>
                <button class="button orange">Orange</button>
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Small</h3>
            <div class="body">
            <button class="button red">Red</button>
                <button class="button small green">Green</button>
                <button class="button small blue">Blue</button>
                <button class="button small yellow">Yellow</button>
                <button class="button small purple">Purple</button>
                <button class="button small orange">Orange</button>
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Button group</h3>
            <div class="body">
                <div class="button-group">
                    <button class="button red">Red</button>
                    <button class="button green">Green</button>
                    <button class="button blue">Blue</button>
                    <button class="button yellow">Yellow</button>
                    <button class="button purple">Purple</button>
                    <button class="button orange">Orange</button>
                </div>
            </div>
        </div>
    </div>

    <h2 class="title mt-8">Inputs</h2>
    <div class="body">
    <div class="card">
            <h3 class="title text-lg">Normal</h3>
            <div class="body">
                <input type="text" class="voyager-input w-full" placeholder="Placeholder" />
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Disabled</h3>
            <div class="body">
                <input type="text" class="voyager-input w-full" disabled placeholder="Placeholder" />
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Small</h3>
            <div class="body">
                <input type="text" class="voyager-input w-full small" placeholder="Placeholder" />
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">With label</h3>
            <div class="body">
                <label class="label" for="labeled-input">Label</label>
                <input type="text" class="voyager-input w-full" id="labeled-input" placeholder="Placeholder" />
            </div>
        </div>
    </div>

    <h2 class="title mt-8">Badges</h2>
    <div class="body">
    <div class="card">
            <h3 class="title text-lg">Normal</h3>
            <div class="body">
                <span class="badge red">Red</span>
                <span class="badge green">Green</span>
                <span class="badge blue">Blue</span>
                <span class="badge yellow">Yellow</span>
                <span class="badge purple">Purple</span>
                <span class="badge orange">Orange</span>
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">Large</h3>
            <div class="body">
                <span class="badge large red">Red</span>
                <span class="badge large green">Green</span>
                <span class="badge large blue">Blue</span>
                <span class="badge large yellow">Yellow</span>
                <span class="badge large purple">Purple</span>
                <span class="badge large orange">Orange</span>
            </div>
        </div>
        <div class="card">
            <h3 class="title text-lg">With Icon</h3>
            <div class="body">
                <span class="badge red">
                    <i class="uil uil-question-circle text-xl"></i>
                    Red
                </span>
                <span class="badge green">
                    <i class="uil uil-angle-double-left text-xl"></i>
                    Green
                </span>
                <span class="badge blue">
                    <i class="uil uil-angle-double-right text-xl"></i>
                    Blue
                </span>
                <span class="badge yellow">
                    <i class="uil uil-question-circle text-xl"></i>
                    Yellow
                </span>
                <span class="badge purple">
                    <i class="uil uil-comment-plus text-xl"></i>
                    Purple
                </span>
                <span class="badge orange">
                    <i class="uil uil-suitcase text-xl"></i>
                    Orange
                </span>
            </div>
        </div>
    </div>

    <h2 class="title mt-8">Alert</h2>
    <div class="body">
    <div class="card">
        <h3 class="title text-lg">Normal</h3>
        <div class="body">
            <div class="alert red mb-5">
                This is an error!
            </div>

            <div class="alert green mb-5">
                This is a success message!
            </div>

            <div class="alert blue mb-5">
                This is an information!
            </div>

            <div class="alert yellow mb-5">
                This is a warning!
            </div>

            <div class="alert purple mb-5">
                This is just purple 🤷‍♂️
            </div>

            <div class="alert orange mb-5">
                And orange 🍊
            </div>
        </div>
    </div>
</div>
    
@endsection