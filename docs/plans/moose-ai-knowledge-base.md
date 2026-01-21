# Moose AI Knowledge Base Plan

## Overview

This document outlines the plan for creating a shared AI knowledge repository for Moose Framework development. The knowledge base enables AI agents (in Cursor, Claude Code, and other tools) to leverage accumulated learnings across all Moose projects while keeping the knowledge base completely separate from client-deliverable codebases.

### Goals

1. **Accelerate development** - AI agents have instant access to Moose patterns and solutions
2. **Cross-project learning** - Learnings from Client A's project help when building Client B's project
3. **Team-wide sharing** - All developers benefit from the same knowledge base
4. **Clean client handoffs** - No traces of the knowledge base in delivered projects
5. **IDE-agnostic** - Works with Cursor, Claude Code, and other AI tools

### Approach

**Developer Machine Setup** - The knowledge base lives on each developer's machine in a standard location (`~/.moose-knowledge/`), completely separate from any project repository.

---

## Phase 1: Repository Structure

Create a new private repository: `modern-tribe/moose-knowledge`

### Proposed Directory Structure

```
moose-knowledge/
├── AGENTS.md                     # Universal AI context file
├── README.md                     # Human-readable overview
│
├── architecture/                 # How Moose works
│   ├── overview.md               # High-level architecture
│   ├── directory-structure.md    # Codebase organization
│   ├── blocks.md                 # Block development patterns
│   ├── full-site-editing.md      # FSE patterns and templates
│   ├── design-system.md          # Figma ↔ code relationship
│   ├── build-system.md           # Webpack, PostCSS, etc.
│   └── php-patterns.md           # PHP conventions and patterns
│
├── patterns/                     # Reference implementations
│   ├── blocks/                   # Block pattern examples
│   │   ├── basic-block.md
│   │   ├── dynamic-block.md
│   │   ├── carousel.md
│   │   ├── accordion.md
│   │   └── ...
│   ├── components/               # Reusable component patterns
│   ├── styling/                  # CSS/PostCSS patterns
│   │   ├── responsive.md
│   │   ├── design-tokens.md
│   │   └── animations.md
│   └── javascript/               # JS patterns
│       ├── alpine-patterns.md
│       └── vanilla-js.md
│
├── solutions/                    # Problem → Solution index
│   ├── by-problem/               # Indexed by problem type
│   │   ├── layout-issues.md
│   │   ├── responsive-challenges.md
│   │   ├── block-editor-quirks.md
│   │   └── performance.md
│   └── by-component/             # Indexed by component
│       ├── navigation.md
│       ├── forms.md
│       ├── media.md
│       └── ...
│
├── learnings/                    # Project retrospectives
│   ├── TEMPLATE.md               # Template for new learnings
│   ├── 2024-project-alpha.md     # Anonymized project learnings
│   ├── 2025-project-beta.md
│   └── ...
│
├── troubleshooting/              # Known issues and fixes
│   ├── common-errors.md
│   ├── editor-issues.md
│   ├── build-problems.md
│   └── deployment.md
│
├── checklists/                   # Development checklists
│   ├── new-block.md
│   ├── new-project-setup.md
│   ├── pre-launch.md
│   └── accessibility.md
│
└── adapters/                     # Per-tool configuration
    ├── cursor/
    │   └── moose.mdc             # Global Cursor rule
    ├── claude-code/
    │   └── setup.md              # Claude Code setup instructions
    └── generic/
        └── context.md            # For any AI tool
```

### Key Files

#### AGENTS.md

The universal AI context file. This is auto-detected by Cursor and Claude Code.

```markdown
# Moose Framework - AI Agent Context

You are assisting with a Moose Framework project. Moose is a WordPress 
Full Site Editing (FSE) framework for building custom websites.

## Quick Reference

- **Stack:** WordPress, PHP 8+, Alpine.js, PostCSS, Webpack
- **Block location:** `wp-content/themes/core/blocks/tribe/`
- **Styling:** PostCSS with design tokens
- **JavaScript:** Alpine.js for interactivity, vanilla JS where needed

## Before Implementing

1. Check `patterns/` for existing reference implementations
2. Check `solutions/` if solving a known problem type
3. Follow the conventions in `architecture/`

## Key Conventions

[To be populated by analyzing the Moose codebase]

## Common Pitfalls

[To be populated from project learnings]
```

#### learnings/TEMPLATE.md

Template for capturing project learnings:

```markdown
# Project Learnings: [Project Codename]

**Date:** YYYY-MM
**Project Type:** [Marketing site / Web app / etc.]
**Notable Features:** [List key features built]

## What Worked Well

- 

## Challenges Encountered

### [Challenge Title]
**Problem:** 
**Solution:** 
**Applicable to:** [When this solution applies]

## New Patterns Discovered

[Any patterns that should be added to patterns/]

## Recommendations for Future Projects

- 
```

---

## Token Usage and Cost Considerations

### Understanding How This Works

**Important:** There is no "training" or "learning" happening with AI tools like Cursor or Claude. These models are pre-trained and frozen. What we're doing is providing **context at inference time**—including relevant information with each request so the AI can reference it.

### What Happens When a New Request Is Made

Every time you send a message to an AI agent, this is what gets sent:

```
Each AI Request
├── System prompt (Cursor/Claude internal)     ~500-2000 tokens
├── AGENTS.md / Cursor rules (always loaded)   ~500-2000 tokens ← You control this
├── Files you @reference or AI reads           Variable
├── Conversation history                       Growing with each message
└── Your current message                       Variable
                                               ─────────────────────────────
                                               Total tokens sent to the model
```

Files marked with `alwaysApply: true` in Cursor rules are loaded on **every single request**, whether or not they're needed. Referenced files (`@patterns/blocks/carousel.md`) are loaded only when explicitly mentioned.

### Keep AGENTS.md Brief

Since AGENTS.md (and always-applied Cursor rules) are loaded on every request, their size directly impacts cost and context window usage.

**Guidelines:**
- **Target:** 1000-1500 tokens maximum for always-loaded content
- **Structure:** Use AGENTS.md as an index with pointers, not complete documentation
- **Content:** Include only essential context—conventions, key paths, common pitfalls
- **Details elsewhere:** Put full patterns and solutions in separate files that are loaded on-demand

**Example of efficient AGENTS.md:**

```markdown
# Moose Framework

WordPress FSE framework. Stack: PHP 8+, Alpine.js, PostCSS.

## Key Paths
- Blocks: `wp-content/themes/core/blocks/tribe/`
- Patterns: See `~/.moose-knowledge/patterns/`

## Before Implementing
1. Check patterns/ for existing implementations
2. Check solutions/ for known fixes

## Critical Conventions
- Mobile-first CSS
- Alpine.js for interactivity
- [3-5 most important rules only]
```

**Avoid this (too long):**

```markdown
# Moose Framework

[500 words of background...]

## Complete Block Development Guide

[Full tutorial with code examples...]

## All Patterns

[Listing every pattern in detail...]
```

### Cost Estimation

| Scenario | Tokens/Request | Requests/Day (5 devs) | Monthly Cost* |
|----------|----------------|----------------------|---------------|
| Lean AGENTS.md (~1000 tokens) | ~1000 | 250 | ~$23 |
| Verbose AGENTS.md (~3000 tokens) | ~3000 | 250 | ~$68 |
| + Average file references | +1500 | 150 | +$20 |

*Estimated at ~$3/M input tokens (Claude API pricing). Actual costs vary by tool and plan.

### Optimization Strategies

1. **Use conditional loading** (Cursor rules)
   ```yaml
   ---
   globs: ["**/blocks/**"]  # Only load for block-related files
   alwaysApply: false
   ---
   ```

2. **Structure for selective reference**
   - Keep always-loaded content minimal
   - Put detailed patterns in separate files
   - Let developers `@reference` what they need

3. **Summarize, don't duplicate**
   - AGENTS.md: "For carousel patterns, see patterns/blocks/carousel.md"
   - Not: Full carousel implementation in AGENTS.md

---

## Phase 2: Initial Content Population

### Step 1: Analyze Current Moose Codebase

Extract and document:
- [ ] Directory structure and conventions
- [ ] Block creation patterns (examine existing blocks)
- [ ] PHP patterns and coding standards
- [ ] CSS/PostCSS conventions
- [ ] JavaScript patterns
- [ ] Build system configuration

### Step 2: Document Core Architecture

Create initial architecture docs:
- [ ] `architecture/overview.md` - High-level Moose architecture
- [ ] `architecture/blocks.md` - How blocks are structured
- [ ] `architecture/design-system.md` - Figma to code workflow
- [ ] `architecture/build-system.md` - Webpack/PostCSS setup

### Step 3: Create Reference Patterns

Document 3-5 exemplary patterns:
- [ ] Basic block pattern
- [ ] Complex block with interactivity
- [ ] Responsive styling pattern
- [ ] Common component pattern

### Step 4: Seed with Known Solutions

Document solutions from team knowledge:
- [ ] Common block editor issues
- [ ] Responsive layout solutions
- [ ] Performance optimizations
- [ ] Accessibility patterns

---

## Phase 3: Developer Onboarding

### One-Time Setup Per Developer

#### 1. Clone the Knowledge Base

```bash
git clone git@github.com:your-org/moose-knowledge.git ~/.moose-knowledge
```

#### 2. Install Cursor Global Rule

```bash
mkdir -p ~/.cursor/rules
cp ~/.moose-knowledge/adapters/cursor/moose.mdc ~/.cursor/rules/
```

#### 3. (Optional) Create Update Alias

Add to `~/.zshrc` or `~/.bashrc`:

```bash
alias moose-update="cd ~/.moose-knowledge && git pull && cd -"
```

### Claude Code Setup

For developers using Claude Code:

1. When starting a Moose project, add `~/.moose-knowledge` to the workspace
2. Or reference `~/.moose-knowledge/AGENTS.md` in project settings

### Other AI Tools

For any AI tool that accepts context:
1. Copy contents of `~/.moose-knowledge/adapters/generic/context.md`
2. Add to the AI tool's system prompt or project context

---

## Phase 4: Cursor Global Rule

Create `adapters/cursor/moose.mdc`:

```markdown
---
description: Moose Framework AI Agent
globs: 
  - "**/wp-content/themes/**"
  - "**/wp-content/plugins/**"
alwaysApply: true
---

# Moose Framework Context

You are working on a Moose Framework project - a WordPress Full Site Editing 
framework for building custom websites.

## Knowledge Base Location

The Moose knowledge base is available at: ~/.moose-knowledge/

When implementing Moose features:
1. First check ~/.moose-knowledge/patterns/ for reference implementations
2. Check ~/.moose-knowledge/solutions/ for known problem solutions
3. Follow conventions documented in ~/.moose-knowledge/architecture/

## Key Patterns

### Block Structure
Blocks live in: wp-content/themes/core/blocks/tribe/[block-name]/
Each block contains:
- block.json - Block registration
- edit.js - Editor component
- render.php - Frontend rendering
- style.pcss - Block styles

### Styling Conventions
- Use PostCSS with design tokens
- Mobile-first responsive approach
- Reference ~/.moose-knowledge/patterns/styling/ for patterns

### JavaScript
- Alpine.js for interactivity
- Vanilla JS for simple interactions
- Reference ~/.moose-knowledge/patterns/javascript/ for patterns

## When You Learn Something New

If you discover a useful pattern or solution not in the knowledge base,
suggest adding it by noting: "This could be added to moose-knowledge/[path]"
```

---

## Phase 5: Contribution Workflow

### Adding New Learnings

After completing a project:

1. **Create a learning document**
   ```bash
   cd ~/.moose-knowledge
   cp learnings/TEMPLATE.md learnings/$(date +%Y)-project-name.md
   ```

2. **Fill in the template** with project learnings

3. **Submit a PR** to the knowledge repo
   ```bash
   git checkout -b add-project-name-learnings
   git add learnings/
   git commit -m "Add learnings from Project Name"
   git push -u origin add-project-name-learnings
   # Create PR in GitHub
   ```

### Adding New Patterns

When a reusable pattern is identified:

1. Create pattern doc in appropriate `patterns/` subdirectory
2. Include:
   - When to use this pattern
   - Complete code example
   - Variations if applicable
   - Related patterns
3. Submit PR for team review

### Contribution Guidelines

- **Anonymize client information** - Use codenames, never client names
- **Include context** - Explain when patterns apply
- **Keep it practical** - Focus on code and solutions, not theory
- **Update, don't duplicate** - Enhance existing docs rather than creating new ones

---

## Phase 6: Maintenance

### Regular Updates

| Task | Frequency | Owner |
|------|-----------|-------|
| Pull latest knowledge base | Weekly | Each developer |
| Review and merge learning PRs | Weekly | Tech lead |
| Audit for outdated content | Quarterly | Team |
| Major knowledge base review | Bi-annually | Team |

### Quality Standards

- All patterns must include working code examples
- Solutions must describe when they apply (and when they don't)
- Learnings must be anonymized before merging

### Versioning

The knowledge base uses semantic versioning:
- **Major:** Breaking changes to structure or conventions
- **Minor:** New patterns, solutions, or significant additions
- **Patch:** Fixes, clarifications, small updates

Developers should periodically update their local clone, especially before starting new projects.

---

## Implementation Timeline

### Week 1: Foundation
- [ ] Create `moose-knowledge` repository
- [ ] Set up directory structure
- [ ] Draft initial AGENTS.md
- [ ] Create Cursor global rule

### Week 2: Core Documentation
- [ ] Analyze Moose codebase and document architecture
- [ ] Create 3-5 reference patterns from existing blocks
- [ ] Document known solutions from team knowledge

### Week 3: Team Rollout
- [ ] Write developer onboarding documentation
- [ ] Set up team on knowledge base
- [ ] Gather initial feedback

### Week 4: Refinement
- [ ] Incorporate feedback
- [ ] Add additional patterns as identified
- [ ] Establish contribution workflow

### Ongoing
- [ ] Capture learnings from each project
- [ ] Continuously improve patterns and solutions
- [ ] Regular maintenance and updates

---

## Success Metrics

How we'll know this is working:

1. **Reduced ramp-up time** - New developers productive on Moose projects faster
2. **Fewer repeated mistakes** - Known issues documented and avoided
3. **Consistent implementations** - Similar features built similarly across projects
4. **Growing knowledge base** - Regular contributions from team members
5. **AI effectiveness** - AI agents provide more accurate Moose-specific guidance

---

## Open Questions

- [ ] Where should the knowledge repo be hosted? (GitHub org, internal Git, etc.)
- [ ] Who are the initial maintainers/reviewers?
- [ ] Should we include Figma-specific documentation?
- [ ] How do we handle knowledge that applies to specific Moose versions?

---

## Appendix: File Templates

### Pattern Document Template

```markdown
# Pattern: [Pattern Name]

## When to Use

[Describe the situation where this pattern applies]

## Implementation

[Complete, working code example]

## Variations

[Any common variations of this pattern]

## Related Patterns

- [Link to related patterns]

## Notes

[Any gotchas or important considerations]
```

### Solution Document Template

```markdown
# Solution: [Problem Title]

## Problem

[Describe the problem]

## When This Applies

[Describe the conditions under which this solution is appropriate]

## Solution

[Step-by-step solution with code]

## Why This Works

[Brief explanation]

## Alternatives Considered

[Other approaches and why they weren't chosen]
```
