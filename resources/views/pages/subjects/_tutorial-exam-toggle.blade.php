<form action="{{ route('subjects.toggle-tutorial-exam', $subject) }}" method="POST" class="subject-tutorial-toggle-form">
    @csrf
    <button
        type="submit"
        class="subject-tutorial-toggle {{ $subject->include_in_tutorial_exam ? 'is-included' : 'is-excluded' }}"
        title="{{ $subject->include_in_tutorial_exam ? 'Exclude from Tutorial Exam' : 'Include in Tutorial Exam' }}"
        aria-label="{{ $subject->include_in_tutorial_exam ? 'Exclude from Tutorial Exam' : 'Include in Tutorial Exam' }}"
    >
        <span class="subject-tutorial-toggle-dot" aria-hidden="true"></span>
        <span>{{ $subject->include_in_tutorial_exam ? 'Included' : 'Excluded' }}</span>
    </button>
</form>

@once
    <style>
        .subject-tutorial-toggle-form { margin: 0; }
        .subject-tutorial-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            min-width: 5.8rem;
            justify-content: center;
            padding: 0.3rem 0.55rem;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
        }
        .subject-tutorial-toggle.is-included {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }
        .subject-tutorial-toggle.is-excluded {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #64748b;
        }
        .subject-tutorial-toggle:hover,
        .subject-tutorial-toggle:focus {
            filter: brightness(0.96);
            outline: 0;
        }
        .subject-tutorial-toggle-dot {
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 50%;
            background: currentColor;
        }
    </style>
@endonce
