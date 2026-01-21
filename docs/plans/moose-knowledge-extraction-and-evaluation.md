# Moose Knowledge Extraction and Evaluation Plan

## Overview

This document outlines the process for:
1. **Extracting knowledge** from completed Moose projects to build the initial knowledge base
2. **Evaluating the knowledge base** using those same projects as test cases
3. **Iteratively improving** the knowledge base based on evaluation results

### Prerequisites

- [Moose AI Knowledge Base](./moose-ai-knowledge-base.md) repository structure is defined
- Access to 5+ completed Moose project codebases
- Access to accompanying Figma design files
- Developers available for brief retrospective interviews

### Goals

1. Bootstrap the knowledge base with real-world patterns and solutions
2. Validate that the knowledge base actually helps AI agents build Moose projects
3. Establish a repeatable process for continuous improvement

---

## Part 1: Knowledge Extraction

### Extraction Sources

| Source | What to Extract | Target Location |
|--------|-----------------|-----------------|
| Project codebases | Block patterns, conventions, reusable code | `patterns/` |
| Project codebases | Problems solved, edge cases | `solutions/` |
| Git history | Major decisions, refactors, lessons learned | `learnings/` |
| Figma files | Design token mappings, component relationships | `architecture/design-system.md` |
| Developer interviews | Non-obvious decisions, gotchas | `troubleshooting/`, `learnings/` |

### Step 1: AI-Assisted Code Analysis

For each completed project, use AI to analyze the codebase and generate initial documentation.

#### Analysis Prompt Template

Use this prompt (or adapt it) when analyzing each project:

```markdown
# Moose Project Analysis

Analyze this Moose project codebase and extract knowledge for our shared knowledge base.

## 1. Block Inventory

For each custom block in `wp-content/themes/core/blocks/tribe/`:
- **Name**: Block name
- **Purpose**: What does it do?
- **Key Features**: Notable functionality
- **Implementation Notes**: Anything reusable or noteworthy
- **Patterns Used**: Alpine.js, specific CSS techniques, etc.

## 2. Styling Patterns

Analyze the CSS/PostCSS files and identify:
- Responsive breakpoint patterns
- Design token usage (CSS custom properties)
- Animation/transition approaches
- Layout patterns (grid, flexbox usage)
- Any browser compatibility handling

## 3. JavaScript Patterns

Document:
- Alpine.js component patterns and conventions
- Event handling approaches
- Third-party library integrations
- Vanilla JS utilities

## 4. PHP Patterns

Identify:
- Block rendering patterns in render.php files
- Data handling (ACF fields, meta, etc.)
- Template part usage
- Any custom functions or utilities

## 5. Solutions Discovered

Look for code that solves non-obvious problems:
- Browser compatibility fixes
- WordPress editor quirks handled
- Performance optimizations
- Accessibility implementations
- Mobile-specific handling

## 6. Potential Gotchas

Identify anything that might trip up future developers:
- Non-obvious dependencies
- Order-of-operations requirements
- Configuration that's easy to miss

Output each section as structured markdown suitable for a knowledge base.
```

#### Running the Analysis

```bash
# For each project
1. Open project in AI-enabled IDE (Cursor, Claude Code)
2. Provide the analysis prompt
3. Let AI explore the codebase
4. Save output as: extraction/project-[name]-analysis.md
5. Human reviews and refines
```

### Step 2: Cross-Project Pattern Identification

After analyzing each project individually, identify patterns that appear across multiple projects.

#### Cross-Project Comparison Prompt

```markdown
# Cross-Project Pattern Analysis

I have analyzed 5 Moose projects. Here are the summaries:

[Paste or reference each project's analysis]

Please identify:

## 1. Common Patterns (appear in 3+ projects)
These should become our core `patterns/` documentation.

## 2. Repeated Solutions (appear in 2+ projects)
These should become our `solutions/` documentation.

## 3. Divergent Approaches
Where did implementations differ? Which approach is preferred and why?

## 4. Unique Innovations
Patterns that appeared in only one project but are valuable enough to standardize.

## 5. Anti-Patterns
Approaches that appeared early but were later improved upon.
```

#### Pattern Prioritization

| Frequency | Action |
|-----------|--------|
| Appears in 4-5 projects | Core pattern → `patterns/` with full documentation |
| Appears in 2-3 projects | Standard pattern → `patterns/` with documentation |
| Appears in 1 project | Evaluate: Is it good? → Add to patterns or note as alternative |
| Inconsistent across projects | Decide on preferred approach, document reasoning |

### Step 3: Figma to Code Mapping

Extract the relationship between Figma designs and code implementations.

#### Figma Analysis Process

For each project with Figma files:

1. **Extract Design Tokens**
   ```
   - Colors (primitives and semantic)
   - Typography scales
   - Spacing values
   - Border radii
   - Shadows
   - Breakpoints
   ```

2. **Map Components to Blocks**
   ```
   Figma Component          →  WordPress Block
   ─────────────────────────────────────────────
   Hero Section             →  tribe/hero
   Card Grid                →  tribe/card-grid
   Testimonial Carousel     →  tribe/carousel (variant)
   ```

3. **Document Translation Patterns**
   ```
   - How Figma auto-layout maps to CSS flexbox/grid
   - How Figma variants map to block variations
   - How Figma component properties map to block attributes
   ```

4. **Note Gaps and Manual Steps**
   ```
   - What required manual interpretation?
   - What doesn't translate cleanly?
   - What conventions fill the gaps?
   ```

#### Figma Analysis Prompt

```markdown
# Figma to Code Analysis

Analyze the relationship between this project's Figma file and its code implementation.

## Design Token Mapping

| Figma Token | CSS Custom Property | Usage |
|-------------|--------------------| ------|
| [token]     | [property]         | [where used] |

## Component to Block Mapping

| Figma Component | WordPress Block | Notes |
|-----------------|-----------------|-------|
| [component]     | [block]         | [translation notes] |

## Translation Patterns

How are these Figma concepts translated to code?
- Auto-layout → ?
- Variants → ?
- Component properties → ?
- Responsive behavior → ?

## Gaps and Manual Steps

What required interpretation or manual work?
```

### Step 4: Developer Retrospectives

Capture knowledge that isn't visible in code through brief developer interviews.

#### Retrospective Questions

```markdown
# Project Retrospective: [Project Name]

Developer: [Name]
Date: [Date]

## Quick Questions (15-20 minutes)

1. **Hardest Part**
   What was the most challenging aspect of this project?

2. **Do Differently**
   What would you do differently if starting this project today?

3. **Hidden Solutions**
   What solutions did you implement that aren't obvious from reading the code?

4. **Gotchas**
   What tripped you up that future developers should know about?

5. **Proud Of**
   What implementation are you most proud of? Why?

6. **Missing Documentation**
   What do you wish had been documented before you started?

## Specific Feature Deep-Dives

[For 2-3 complex features, ask:]
- How did you approach this?
- What alternatives did you consider?
- What would you tell someone implementing something similar?
```

#### Retrospective Output

Convert interview notes into:
- `learnings/[year]-project-[name].md` - Project-specific learnings
- Updates to `solutions/` - Generalizable solutions discovered
- Updates to `troubleshooting/` - Gotchas and fixes

### Step 5: Compile Initial Knowledge Base

Organize all extracted knowledge into the repository structure.

#### Compilation Checklist

```
For patterns/:
- [ ] Review all identified patterns from cross-project analysis
- [ ] Create one file per pattern with: When to use, Implementation, Variations
- [ ] Ensure code examples are complete and tested
- [ ] Add cross-references between related patterns

For solutions/:
- [ ] Compile all solutions from project analyses
- [ ] Organize by problem type (by-problem/) and component (by-component/)
- [ ] Include: Problem, When it applies, Solution, Why it works

For architecture/:
- [ ] Create overview.md from common project structures
- [ ] Create design-system.md from Figma analysis
- [ ] Document build system patterns

For learnings/:
- [ ] One file per project with anonymized learnings
- [ ] Extract generalizable insights to other sections

For troubleshooting/:
- [ ] Compile gotchas from all sources
- [ ] Organize by category (editor, build, deployment, etc.)

For AGENTS.md:
- [ ] Write concise summary (target: 1000-1500 tokens)
- [ ] Include most critical conventions
- [ ] Point to detailed docs for more info
```

---

## Part 2: Knowledge Base Evaluation

Use the same completed projects as test cases to evaluate how well the knowledge base helps AI agents.

### Evaluation Concept

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Figma Design   │────▶│  AI + Knowledge │────▶│  Generated Code │
│  + Requirements │     │  Base           │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                        │
                                                        ▼ Compare
                                               ┌─────────────────┐
                                               │  Actual Project │
                                               │  Implementation │
                                               │  (Ground Truth) │
                                               └─────────────────┘
```

### Evaluation Repository Structure

Create an evaluation suite alongside the knowledge base:

```
moose-knowledge/
├── AGENTS.md
├── patterns/
├── solutions/
├── ...
└── evaluation/                    # Evaluation suite
    ├── README.md                  # How to run evaluations
    ├── test-cases/
    │   ├── project-alpha/
    │   │   ├── carousel-block.md
    │   │   ├── hero-section.md
    │   │   └── ...
    │   ├── project-beta/
    │   │   └── ...
    │   └── ...
    ├── ground-truth/              # Reference implementations
    │   ├── project-alpha/
    │   │   ├── carousel/          # Actual block code
    │   │   └── ...
    │   └── ...
    ├── results/                   # Evaluation run results
    │   ├── 2025-01-21/
    │   │   ├── scores.md
    │   │   └── gaps.md
    │   └── ...
    └── scoring/
        ├── rubric.md              # Scoring criteria
        └── template.md            # Result template
```

### Creating Test Cases

#### Test Case Template

Create one test case per significant feature from each project:

```markdown
# Test Case: [Feature Name]

**Project:** [Project codename]
**Category:** [Block / Component / Page Template / etc.]
**Difficulty:** [Simple / Medium / Complex]

## Context

[Brief description of what this feature does and why it was built]

## Input

### Figma Reference

[Link to Figma file/frame, or embedded screenshot]

### Requirements

- [Requirement 1]
- [Requirement 2]
- [Requirement 3]
- ...

### Prompt for AI

```
[The exact prompt to give the AI agent]
```

## Ground Truth

### Location

`ground-truth/project-[name]/[feature]/`

### Key Implementation Details

- [Detail 1: e.g., "Uses Alpine.js x-data for state"]
- [Detail 2: e.g., "CSS Grid for layout, Flexbox for items"]
- [Detail 3: e.g., "Intersection Observer for lazy loading"]
- ...

### Critical Requirements

These must be present for a passing score:
- [ ] [Critical requirement 1]
- [ ] [Critical requirement 2]
- ...

## Evaluation Criteria

| Criterion | Weight | Description |
|-----------|--------|-------------|
| Structure | 20% | Follows Moose block conventions |
| Functionality | 30% | Meets stated requirements |
| Code Quality | 20% | Clean, maintainable, follows patterns |
| Similarity | 15% | Approaches ground truth implementation |
| Usability | 15% | Works as-is or needs minimal fixes |

## Notes

[Any special considerations for this test case]
```

#### Test Case Selection

For each project, select test cases that cover:

| Category | Count | Selection Criteria |
|----------|-------|-------------------|
| Simple blocks | 1-2 | Basic blocks that should be easy |
| Complex blocks | 2-3 | Blocks with interactivity, state |
| Styling challenges | 1-2 | Responsive, animations, layouts |
| Integration points | 1-2 | Forms, third-party, data handling |

**Target: 5-8 test cases per project, 25-40 total across 5 projects**

### Scoring Rubric

#### Score Definitions

| Score | Meaning |
|-------|---------|
| 0 | Not attempted or completely wrong |
| 1 | Major issues, would need significant rework |
| 2 | Partial success, needs moderate fixes |
| 3 | Good implementation, minor issues only |
| 4 | Excellent, matches or exceeds ground truth |

#### Evaluation Criteria Details

```markdown
## Structure (0-4)
Does the implementation follow Moose conventions?

- 0: Wrong file structure, missing required files
- 1: Basic structure but missing key elements
- 2: Correct structure, some convention violations
- 3: Follows conventions with minor deviations
- 4: Perfect adherence to Moose patterns

## Functionality (0-4)
Does it meet the stated requirements?

- 0: Doesn't work at all
- 1: Partially works, major features missing
- 2: Core functionality works, some requirements missed
- 3: All requirements met with minor issues
- 4: All requirements met, handles edge cases

## Code Quality (0-4)
Is the code clean and maintainable?

- 0: Unreadable, major issues
- 1: Works but poorly structured
- 2: Acceptable quality, some issues
- 3: Good quality, follows patterns
- 4: Excellent, exemplary code

## Similarity (0-4)
How close to the ground truth approach?

- 0: Completely different approach
- 1: Different approach, may work but not preferred
- 2: Similar approach with notable differences
- 3: Very similar, minor differences
- 4: Matches ground truth approach

## Usability (0-4)
Could this be used in production?

- 0: Would not work at all
- 1: Needs significant rework
- 2: Needs moderate fixes
- 3: Needs minor fixes
- 4: Production-ready as-is
```

#### Overall Score Calculation

```
Total Score = (Structure × 0.20) + (Functionality × 0.30) + 
              (Code Quality × 0.20) + (Similarity × 0.15) + 
              (Usability × 0.15)

Maximum: 4.0
```

| Score Range | Interpretation |
|-------------|----------------|
| 3.5 - 4.0 | Excellent - Knowledge base working very well |
| 3.0 - 3.4 | Good - Minor improvements needed |
| 2.5 - 2.9 | Acceptable - Some gaps to address |
| 2.0 - 2.4 | Needs Work - Significant gaps identified |
| < 2.0 | Poor - Major knowledge base improvements needed |

### Running Evaluations

#### Baseline Evaluation (No Knowledge Base)

Before testing with the knowledge base, establish a baseline:

```
1. Start fresh AI conversation WITHOUT knowledge base
2. Provide Figma reference and requirements only
3. Ask AI to implement the feature
4. Score the output
5. Record as baseline
```

This shows how much value the knowledge base adds.

#### Knowledge Base Evaluation

```
1. Start fresh AI conversation WITH knowledge base loaded
2. Provide same Figma reference and requirements
3. Ask AI to implement the feature
4. Score the output
5. Compare to baseline and ground truth
```

#### Evaluation Session Template

```markdown
# Evaluation Session: [Date]

## Configuration
- Knowledge Base Version: [commit hash or version]
- AI Tool: [Cursor / Claude Code / etc.]
- Model: [if known]
- Evaluator: [Name]

## Test Cases Evaluated

| Test Case | Baseline | With KB | Improvement | Notes |
|-----------|----------|---------|-------------|-------|
| [case 1]  | [score]  | [score] | [+/-]       | [notes] |
| [case 2]  | [score]  | [score] | [+/-]       | [notes] |
| ...       | ...      | ...     | ...         | ...   |

## Aggregate Scores

| Metric | Baseline | With KB | Improvement |
|--------|----------|---------|-------------|
| Average Score | X.X | X.X | +X.X |
| Pass Rate (≥3.0) | X% | X% | +X% |
| Perfect Score (≥3.5) | X% | X% | +X% |

## Gaps Identified

### High Priority (caused score < 2.5)

1. **[Gap]**: [Description]
   - Affected test cases: [list]
   - Suggested fix: [what to add/change in knowledge base]

2. ...

### Medium Priority (caused score 2.5-3.0)

1. ...

### Low Priority (minor improvements)

1. ...

## Action Items

- [ ] [Action item 1]
- [ ] [Action item 2]
- ...
```

### Improvement Loop

```
┌─────────────────────────────────────────────────────────────┐
│                    Improvement Cycle                         │
│                                                              │
│   ┌──────────────┐                                          │
│   │ Run Baseline │ (first time only)                        │
│   └──────┬───────┘                                          │
│          ▼                                                  │
│   ┌──────────────┐                                          │
│   │ Run Eval     │◀─────────────────────────────────┐       │
│   │ with KB      │                                  │       │
│   └──────┬───────┘                                  │       │
│          ▼                                          │       │
│   ┌──────────────┐                                  │       │
│   │ Score &      │                                  │       │
│   │ Analyze      │                                  │       │
│   └──────┬───────┘                                  │       │
│          ▼                                          │       │
│   ┌──────────────┐     ┌──────────────┐            │       │
│   │ Identify     │────▶│ Update       │────────────┘       │
│   │ Gaps         │     │ Knowledge    │                     │
│   └──────────────┘     │ Base         │                     │
│                        └──────────────┘                     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

#### Iteration Cadence

| Phase | Frequency | Focus |
|-------|-----------|-------|
| Initial build | Daily | Rapid iteration on major gaps |
| Stabilization | Weekly | Address medium-priority gaps |
| Maintenance | Monthly | Minor improvements, new learnings |

### MCP Server Evaluation (Optional)

If implementing the MCP server, add these evaluations:

#### Tool Usage Tracking

```markdown
## MCP Tool Usage Analysis

| Test Case | Expected Tools | Actual Tools Used | Appropriate? |
|-----------|----------------|-------------------|--------------|
| Carousel  | search_patterns, get_pattern | [what AI used] | Yes/No |
| Hero      | search_patterns | [what AI used] | Yes/No |
| ...       | ...            | ...               | ...          |

### Observations

- Did the AI use tools when it should have?
- Did it over-rely on tools vs. its own knowledge?
- Were tool responses helpful?
```

#### Search Quality Evaluation

```markdown
## Search Quality Analysis

| Query | Expected Top Results | Actual Top Results | Relevance Score |
|-------|---------------------|-------------------|-----------------|
| "carousel with autoplay" | patterns/blocks/carousel.md | [actual] | X/5 |
| "responsive grid" | patterns/styling/responsive.md | [actual] | X/5 |
| ...   | ...                 | ...               | ...             |

### Search Improvements Needed

- [Improvement 1]
- [Improvement 2]
```

---

## Implementation Timeline

### Week 1-2: Extraction

| Day | Task |
|-----|------|
| 1-2 | Run AI analysis on first 2 projects |
| 3-4 | Run AI analysis on remaining 3 projects |
| 5 | Cross-project pattern identification |
| 6-7 | Figma analysis for all projects |
| 8-9 | Developer retrospective interviews |
| 10 | Compile initial knowledge base |

### Week 3: Evaluation Setup

| Day | Task |
|-----|------|
| 1-2 | Create test case templates and rubric |
| 3-4 | Write test cases for 2 projects |
| 5-6 | Write test cases for remaining 3 projects |
| 7 | Set up evaluation infrastructure |

### Week 4: Initial Evaluation Cycle

| Day | Task |
|-----|------|
| 1 | Run baseline evaluations (no KB) |
| 2-3 | Run evaluations with knowledge base |
| 4 | Analyze results, identify gaps |
| 5-6 | Update knowledge base |
| 7 | Re-run evaluations, measure improvement |

### Ongoing

- Weekly evaluation runs during active development
- Monthly evaluation runs during maintenance
- Update knowledge base after each project completion

---

## Appendix: Templates

### Project Analysis Output Template

```markdown
# Project Analysis: [Project Codename]

**Analyzed:** [Date]
**Analyzer:** [Name/AI]

## Block Inventory

| Block | Purpose | Key Patterns | Notes |
|-------|---------|--------------|-------|
| tribe/hero | Full-width hero section | Alpine.js, responsive images | [notes] |
| ... | ... | ... | ... |

## Patterns Identified

### Styling Patterns

1. **[Pattern Name]**
   - Where used: [files]
   - Description: [what it does]
   - Code example: [snippet]

### JavaScript Patterns

1. **[Pattern Name]**
   - ...

### PHP Patterns

1. **[Pattern Name]**
   - ...

## Solutions Found

| Problem | Solution | Location |
|---------|----------|----------|
| [problem] | [solution] | [file] |

## Gotchas Discovered

1. [Gotcha 1]
2. [Gotcha 2]

## Recommendations for Knowledge Base

- Add to patterns/: [suggestions]
- Add to solutions/: [suggestions]
- Add to troubleshooting/: [suggestions]
```

### Evaluation Results Template

```markdown
# Evaluation Results: [Test Case Name]

**Date:** [Date]
**Evaluator:** [Name]
**Knowledge Base Version:** [version]

## Scores

| Criterion | Score (0-4) | Notes |
|-----------|-------------|-------|
| Structure | X | [notes] |
| Functionality | X | [notes] |
| Code Quality | X | [notes] |
| Similarity | X | [notes] |
| Usability | X | [notes] |
| **Weighted Total** | **X.X** | |

## Generated Implementation

[Link to or embed the AI-generated code]

## Comparison to Ground Truth

### What Matched
- [match 1]
- [match 2]

### What Differed
- [difference 1]: Ground truth did X, generated did Y
- [difference 2]: ...

### What Was Missing
- [missing 1]
- [missing 2]

## Knowledge Base Gaps Identified

| Gap | Impact | Suggested Fix |
|-----|--------|---------------|
| [gap] | [high/medium/low] | [fix] |

## Improvement Suggestions

1. [suggestion 1]
2. [suggestion 2]
```

### Gap Tracking Template

```markdown
# Knowledge Base Gap Tracker

## Open Gaps

| ID | Gap Description | Discovered | Priority | Test Cases Affected | Status |
|----|-----------------|------------|----------|---------------------|--------|
| G001 | Missing carousel animation patterns | 2025-01-21 | High | carousel-block, slider | Open |
| G002 | ... | ... | ... | ... | ... |

## Resolved Gaps

| ID | Gap Description | Resolved | Resolution |
|----|-----------------|----------|------------|
| G000 | Example resolved gap | 2025-01-20 | Added patterns/animations.md |

## Gap Resolution Process

1. Identify gap from evaluation
2. Create entry in tracker
3. Determine fix (new doc, update existing, etc.)
4. Implement fix
5. Re-run affected test cases
6. If improved, mark resolved
```
