<!--
Sync Impact Report
- Version change: scaffold/unversioned -> 1.0.0
- Modified principles: placeholder principles -> five Clean Code principles
- Added sections: Project Constraints; Development Workflow
- Removed sections: none
- Follow-up TODOs: RATIFICATION_DATE requires confirmation of the original adoption date
-->

# FDC Constitution

## Core Principles

### I. Clarity Over Cleverness
Code MUST communicate its intent through descriptive names, small focused functions,
and straightforward control flow. Abbreviations, hidden side effects, and clever
constructs are prohibited when a simpler expression is available. This keeps the
small codebase readable and reduces the cost of every future change.

### II. Single Responsibility
Each module, function, and class MUST have one clear reason to change. A unit that
coordinates unrelated concerns MUST be split before new behavior is added. This
limits coupling and makes behavior easier to inspect locally.

### III. Minimal Design
The project MUST implement only the behavior required by an accepted request.
Abstractions, dependencies, configuration, and extension points MUST have a
current concrete use. YAGNI and the simplest solution that preserves correctness
take precedence over speculative flexibility.

### IV. Explicit Contracts and Safe Errors
Inputs, outputs, side effects, and failure behavior MUST be explicit at the owning
boundary. Code MUST validate external input at that boundary and MUST fail with a
clear, actionable error rather than silently recovering or returning ambiguous
state. This makes the system predictable without requiring readers to infer rules.

### V. No Automated Test Suite
Automated tests MUST NOT be added to this project. Changes MUST instead be checked
with focused manual verification and the repository's available static, build, or
runtime checks when applicable. This constraint keeps the intentionally small
project aligned with its maintenance budget; any change to it requires a
constitution amendment.

## Project Constraints

The project MUST remain small and simple. New dependencies, layers, generated
artifacts, and configuration MUST be justified by a concrete requirement and kept
to the smallest viable scope. Existing patterns and platform capabilities MUST be
preferred over introducing new frameworks or abstractions.

## Development Workflow

Every change MUST identify the behavior it affects, make the smallest coherent
edit, and perform focused manual verification. Reviews MUST check naming,
responsibility boundaries, unnecessary complexity, input validation, and error
clarity. Automated test files, test runners, and test-only dependencies MUST NOT
be introduced.

## Governance

This constitution is the highest-level project guidance and supersedes conflicting
local practices. Amendments MUST document the reason, affected principles, and
expected impact; they require review before adoption. The version follows
Semantic Versioning: MAJOR for backward-incompatible governance changes, MINOR for
new or materially expanded principles or sections, and PATCH for clarifications
that do not change obligations. The Last Amended date MUST use ISO format and be
updated for every adopted amendment. Each change review MUST verify compliance
with these principles and record any justified exception explicitly.

**Version**: 1.0.0 | **Ratified**: TODO(RATIFICATION_DATE): confirm original adoption date | **Last Amended**: 2026-08-18
