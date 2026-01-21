# Moose MCP Server Plan

## Overview

This document outlines an optional MCP (Model Context Protocol) server that enhances the [Moose AI Knowledge Base](./moose-ai-knowledge-base.md) with structured queries, semantic search, and automated contribution workflows.

### Prerequisites

This plan builds on the Moose Knowledge Base. Complete the knowledge base setup first before implementing the MCP server.

### Why Add an MCP Server?

| Without MCP Server | With MCP Server |
|-------------------|-----------------|
| AI reads static markdown files | AI queries structured tools |
| Full-text search depends on AI | Purpose-built search (semantic optional) |
| Manual git pull for updates | Can fetch on demand |
| AI must parse file structure | Structured responses |
| No contribution automation | Can auto-create PRs |

### When to Add This

Consider adding the MCP server when:
- The knowledge base grows beyond 100+ documents
- Developers report difficulty finding relevant patterns
- You want semantic search ("find similar patterns")
- You want automated contribution workflows
- Multiple teams are using the knowledge base

---

## Architecture

### High-Level Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                        Developer's Machine                          │
│                                                                     │
│  ┌─────────────┐     ┌─────────────────────┐     ┌───────────────┐ │
│  │   Cursor    │────▶│  Moose MCP Server   │────▶│ ~/.moose-     │ │
│  │ Claude Code │     │  (local process)    │     │   knowledge/  │ │
│  │   Claude    │     │                     │     │               │ │
│  └─────────────┘     └─────────────────────┘     └───────────────┘ │
│                              │                                      │
└──────────────────────────────│──────────────────────────────────────┘
                               │
                               ▼ (optional: for add_learning)
                      ┌─────────────────┐
                      │   GitHub API    │
                      │  (create PRs)   │
                      └─────────────────┘
```

### Architecture Options

#### Option A: Lightweight (Recommended Starting Point)

Local process that reads from the git-cloned knowledge base.

```
moose-mcp-server/
├── src/
│   ├── index.ts              # MCP server entry point
│   ├── tools/
│   │   ├── search.ts         # Full-text search
│   │   ├── get-pattern.ts    # Retrieve specific pattern
│   │   ├── list-patterns.ts  # List available patterns
│   │   └── add-learning.ts   # Submit new learning
│   └── knowledge/
│       ├── loader.ts         # Load markdown files from disk
│       └── indexer.ts        # Build search index
├── package.json
└── README.md
```

**Storage:** Same `~/.moose-knowledge/` git repo  
**Search:** Full-text using minisearch  
**Hosting:** Local process on developer machine

#### Option B: Semantic Search (Future Enhancement)

Adds embeddings-based search for better relevance.

```
moose-mcp-server/
├── src/
│   ├── index.ts
│   ├── tools/
│   │   ├── semantic-search.ts   # Embeddings-based search
│   │   ├── get-pattern.ts
│   │   ├── list-patterns.ts
│   │   └── add-learning.ts
│   ├── embeddings/
│   │   ├── indexer.ts           # Index knowledge into vectors
│   │   └── search.ts            # Query vector DB
│   └── db/
│       └── client.ts            # Vector DB client
├── scripts/
│   └── reindex.ts               # Re-index on knowledge base changes
└── package.json
```

**Storage:** Markdown files indexed into vector database  
**Search:** Semantic similarity (finds relevant content even without exact keyword matches)  
**Vector DB Options:** ChromaDB (local), Pinecone, Weaviate, Qdrant

#### Option C: Hosted (Team-Wide Access)

Central server that all developers connect to.

```
┌─────────────────┐         ┌─────────────────────┐
│ Developer A     │────────▶│                     │
│ (Cursor)        │         │  Moose MCP Server   │
├─────────────────┤         │  (Cloudflare/       │
│ Developer B     │────────▶│   Railway/etc.)     │
│ (Claude Code)   │         │                     │
├─────────────────┤         │         │           │
│ Developer C     │────────▶│         ▼           │
│ (Claude)        │         │  ┌─────────────┐    │
└─────────────────┘         │  │ Vector DB   │    │
                            │  │ + Git Clone │    │
                            │  └─────────────┘    │
                            └─────────────────────┘
```

**Pros:** Single source of truth, automatic updates  
**Cons:** Requires hosting, network dependency

---

## MCP Tools Specification

### Tool 1: `moose:search_patterns`

Search the knowledge base for patterns, solutions, or documentation.

```typescript
{
  name: "search_patterns",
  description: "Search the Moose knowledge base for patterns, solutions, or documentation relevant to your task",
  inputSchema: {
    type: "object",
    properties: {
      query: {
        type: "string",
        description: "What are you looking for? e.g., 'carousel block with autoplay', 'responsive grid layout'"
      },
      category: {
        type: "string",
        enum: ["patterns", "solutions", "architecture", "troubleshooting", "all"],
        default: "all",
        description: "Category to search within"
      },
      limit: {
        type: "number",
        default: 5,
        description: "Maximum number of results to return"
      }
    },
    required: ["query"]
  }
}
```

**Example Usage:**

```
AI calls: moose:search_patterns({ 
  query: "responsive image gallery with lightbox",
  category: "patterns"
})

Response:
{
  results: [
    {
      title: "Gallery Block Pattern",
      path: "patterns/blocks/gallery.md",
      relevance: 0.92,
      excerpt: "A responsive gallery block with optional lightbox functionality using Alpine.js..."
    },
    {
      title: "Lightbox JavaScript Pattern",
      path: "patterns/javascript/lightbox.md",
      relevance: 0.85,
      excerpt: "Implementing lightbox behavior with Alpine.js for image galleries..."
    }
  ]
}
```

---

### Tool 2: `moose:get_pattern`

Retrieve the full content of a specific pattern or document.

```typescript
{
  name: "get_pattern",
  description: "Get the full content of a specific pattern, solution, or document from the knowledge base",
  inputSchema: {
    type: "object",
    properties: {
      path: {
        type: "string",
        description: "Path to the document, e.g., 'patterns/blocks/carousel.md'"
      }
    },
    required: ["path"]
  }
}
```

**Example Usage:**

```
AI calls: moose:get_pattern({ path: "patterns/blocks/carousel.md" })

Response:
{
  title: "Carousel Block Pattern",
  path: "patterns/blocks/carousel.md",
  content: "# Carousel Block Pattern\n\n## When to Use\n\nUse this pattern when...\n\n## Implementation\n\n```php\n// render.php\n...",
  lastUpdated: "2025-01-15"
}
```

---

### Tool 3: `moose:list_patterns`

List all available patterns in a category.

```typescript
{
  name: "list_patterns",
  description: "List all available patterns in a specific category",
  inputSchema: {
    type: "object",
    properties: {
      category: {
        type: "string",
        enum: ["blocks", "components", "styling", "javascript", "solutions", "architecture"],
        description: "Category to list patterns from"
      }
    },
    required: ["category"]
  }
}
```

**Example Usage:**

```
AI calls: moose:list_patterns({ category: "blocks" })

Response:
{
  category: "blocks",
  patterns: [
    { name: "Accordion", path: "patterns/blocks/accordion.md", description: "Expandable content sections" },
    { name: "Carousel", path: "patterns/blocks/carousel.md", description: "Image/content slider with navigation" },
    { name: "Gallery", path: "patterns/blocks/gallery.md", description: "Responsive image gallery with lightbox" },
    { name: "Hero", path: "patterns/blocks/hero.md", description: "Full-width hero section with CTA" },
    { name: "Tabs", path: "patterns/blocks/tabs.md", description: "Tabbed content interface" }
  ]
}
```

---

### Tool 4: `moose:get_solution`

Find a documented solution for a specific problem.

```typescript
{
  name: "get_solution",
  description: "Find a documented solution for a specific problem or challenge",
  inputSchema: {
    type: "object",
    properties: {
      problem: {
        type: "string",
        description: "Describe the problem you're trying to solve, e.g., 'Block styles not showing in editor'"
      }
    },
    required: ["problem"]
  }
}
```

**Example Usage:**

```
AI calls: moose:get_solution({ 
  problem: "Block styles not applying in the editor preview" 
})

Response:
{
  found: true,
  solution: {
    title: "Editor Style Loading Issues",
    path: "solutions/by-problem/block-editor-quirks.md#editor-styles",
    problem: "Block styles appear on frontend but not in the editor",
    solution: "Ensure styles are enqueued with `enqueue_block_editor_assets` and use proper selector specificity...",
    relatedPatterns: ["patterns/styling/editor-styles.md"]
  }
}
```

---

### Tool 5: `moose:add_learning`

Submit a new learning or pattern to the knowledge base (creates a PR).

```typescript
{
  name: "add_learning",
  description: "Submit a new learning, pattern, or solution to the knowledge base. Creates a pull request for review.",
  inputSchema: {
    type: "object",
    properties: {
      type: {
        type: "string",
        enum: ["pattern", "solution", "learning"],
        description: "Type of contribution"
      },
      title: {
        type: "string",
        description: "Title for the new entry"
      },
      content: {
        type: "string",
        description: "Full markdown content for the entry"
      },
      suggested_path: {
        type: "string",
        description: "Suggested file path, e.g., 'patterns/blocks/my-new-pattern.md'"
      },
      project_context: {
        type: "string",
        description: "Optional: anonymized context about which project this learning came from"
      }
    },
    required: ["type", "title", "content"]
  }
}
```

**Example Usage:**

```
AI calls: moose:add_learning({
  type: "solution",
  title: "Fixing Safari Flexbox Gap Issues",
  content: "# Fixing Safari Flexbox Gap Issues\n\n## Problem\n\nSafari versions below 14.1 don't support the `gap` property on flexbox...\n\n## Solution\n\nUse margin-based spacing with negative margins on the container...",
  suggested_path: "solutions/by-problem/safari-flexbox-gap.md"
})

Response:
{
  success: true,
  prUrl: "https://github.com/your-org/moose-knowledge/pull/42",
  message: "Created PR #42: Add solution for Safari Flexbox Gap Issues"
}
```

---

### Tool 6: `moose:get_checklist`

Retrieve a development checklist for a specific task.

```typescript
{
  name: "get_checklist",
  description: "Get a development checklist for a specific task type",
  inputSchema: {
    type: "object",
    properties: {
      task: {
        type: "string",
        enum: ["new-block", "new-project", "pre-launch", "accessibility", "performance"],
        description: "Type of checklist to retrieve"
      }
    },
    required: ["task"]
  }
}
```

**Example Usage:**

```
AI calls: moose:get_checklist({ task: "new-block" })

Response:
{
  title: "New Block Checklist",
  items: [
    { task: "Create block directory structure", done: false },
    { task: "Define block.json with proper attributes", done: false },
    { task: "Implement render.php with accessibility", done: false },
    { task: "Create edit.js for editor preview", done: false },
    { task: "Add responsive styles in style.pcss", done: false },
    { task: "Add editor-specific styles if needed", done: false },
    { task: "Test in all major browsers", done: false },
    { task: "Test keyboard navigation", done: false },
    { task: "Verify responsive behavior", done: false }
  ]
}
```

---

## Token Usage and Cost Considerations

### Understanding How This Works

**Important:** There is no "training" or "learning" happening. AI models are pre-trained and frozen. Both the static knowledge base and MCP server approaches provide **context at inference time**—including relevant information with each request.

The key difference is **when** and **how much** knowledge is loaded.

### What Happens When a Request Is Made

#### Static Knowledge Base (AGENTS.md approach)

```
Each AI Request
├── System prompt                              ~500-2000 tokens
├── AGENTS.md / rules (ALWAYS loaded)          ~1000-3000 tokens ← Every request
├── @referenced files                          Variable
├── Conversation history                       Growing
└── Your message                               Variable
```

**Characteristic:** Always-loaded content is sent on every request, whether or not it's needed.

#### MCP Server Approach

```
Each AI Request
├── System prompt                              ~500-2000 tokens
├── MCP tool definitions (lightweight)         ~300-500 tokens ← Just schemas
├── Tool results (ONLY when tools called)      Variable ← On-demand
├── Conversation history                       Growing
└── Your message                               Variable
```

**Characteristic:** Knowledge is fetched on-demand. The AI decides when to call tools, and only relevant excerpts are returned.

### Efficiency Comparison

| Scenario | Static Files | MCP Server |
|----------|--------------|------------|
| Simple question, no Moose context needed | Loads AGENTS.md anyway (~2000 tokens) | Only tool definitions (~300 tokens) |
| Need one specific pattern | Load entire file (~1000 tokens) | Return targeted excerpt (~300 tokens) |
| Search across knowledge base | AI reads multiple files (~5000+ tokens) | Single search returns top matches (~500 tokens) |

**MCP is more token-efficient** because it's **pull-based** (fetch what's needed) rather than **push-based** (always load everything).

### Cost Comparison

Assumptions: 5 developers, ~50 AI requests/day each, Claude API pricing (~$3/M input tokens)

| Approach | Daily Tokens | Monthly Cost |
|----------|--------------|--------------|
| Static (lean AGENTS.md) | ~400,000 | ~$36 |
| Static (verbose AGENTS.md + file refs) | ~650,000 | ~$59 |
| **MCP Server** | ~120,000 | **~$11** |

MCP can be **3-5x more cost-efficient** because knowledge is loaded only when needed.

### MCP-Specific Optimizations

1. **Return excerpts, not full documents**
   ```typescript
   return {
     title: "Carousel Pattern",
     excerpt: content.slice(0, 500),  // First 500 chars
     path: "patterns/blocks/carousel.md",  // AI can request full if needed
   };
   ```

2. **Two-step retrieval pattern**
   - `search_patterns` → Returns titles + short excerpts (~100 tokens each)
   - `get_pattern` → Returns full content (only called if needed)

3. **Limit result counts**
   ```typescript
   // Cap results to avoid token explosion
   results.slice(0, 5)
   ```

4. **Pre-generate summaries**
   - Index documents with short summaries
   - Return summaries in search results
   - Full content only on explicit request

### When MCP Adds Value

The MCP server is worth the implementation effort when:
- Knowledge base exceeds ~50 documents
- Developers frequently search for patterns
- Token costs are a concern
- You want precise, targeted retrieval

For smaller knowledge bases, the static AGENTS.md approach is simpler and the cost difference is minimal.

---

## Implementation Guide

### Technology Stack

| Component | Recommended | Alternatives |
|-----------|-------------|--------------|
| Runtime | Node.js 20+ | Bun |
| MCP SDK | @modelcontextprotocol/sdk | - |
| Search (simple) | minisearch | flexsearch, fuse.js |
| Search (semantic) | OpenAI embeddings + Pinecone | Cohere + ChromaDB |
| Git operations | simple-git | isomorphic-git, GitHub API |
| Markdown parsing | gray-matter + marked | remark |

### Development Steps

#### Step 1: Scaffold the Server

```bash
mkdir moose-mcp-server && cd moose-mcp-server
npm init -y
npm install @modelcontextprotocol/sdk minisearch gray-matter glob
npm install -D typescript @types/node tsx
```

#### Step 2: Create Server Entry Point

```typescript
// src/index.ts
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { registerTools } from "./tools/index.js";
import { loadKnowledgeBase } from "./knowledge/loader.js";

const server = new McpServer({
  name: "moose-knowledge",
  version: "1.0.0",
});

// Load knowledge base and build search index
const knowledge = await loadKnowledgeBase();

// Register all tools
registerTools(server, knowledge);

// Start server
const transport = new StdioServerTransport();
await server.connect(transport);
```

#### Step 3: Implement Knowledge Loader

```typescript
// src/knowledge/loader.ts
import { glob } from "glob";
import matter from "gray-matter";
import { readFileSync } from "fs";
import MiniSearch from "minisearch";
import { homedir } from "os";
import { join } from "path";

const KNOWLEDGE_PATH = join(homedir(), ".moose-knowledge");

export interface Document {
  id: string;
  path: string;
  title: string;
  content: string;
  category: string;
  excerpt: string;
}

export async function loadKnowledgeBase() {
  const files = await glob("**/*.md", { cwd: KNOWLEDGE_PATH });
  
  const documents: Document[] = files.map((file) => {
    const fullPath = join(KNOWLEDGE_PATH, file);
    const raw = readFileSync(fullPath, "utf-8");
    const { data, content } = matter(raw);
    
    return {
      id: file,
      path: file,
      title: data.title || extractTitle(content) || file,
      content,
      category: file.split("/")[0],
      excerpt: content.slice(0, 200) + "...",
    };
  });

  // Build search index
  const searchIndex = new MiniSearch({
    fields: ["title", "content"],
    storeFields: ["title", "path", "category", "excerpt"],
  });
  
  searchIndex.addAll(documents);

  return { documents, searchIndex };
}

function extractTitle(content: string): string | null {
  const match = content.match(/^#\s+(.+)$/m);
  return match ? match[1] : null;
}
```

#### Step 4: Implement Tools

```typescript
// src/tools/search.ts
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";

export function registerSearchTool(server: McpServer, knowledge: Knowledge) {
  server.tool(
    "search_patterns",
    "Search the Moose knowledge base for patterns, solutions, or documentation",
    {
      query: { type: "string", description: "Search query" },
      category: { type: "string", description: "Category filter" },
      limit: { type: "number", description: "Max results" },
    },
    async ({ query, category = "all", limit = 5 }) => {
      let results = knowledge.searchIndex.search(query, { limit: limit * 2 });
      
      if (category !== "all") {
        results = results.filter((r) => r.category === category);
      }
      
      return {
        content: [{
          type: "text",
          text: JSON.stringify({
            results: results.slice(0, limit).map((r) => ({
              title: r.title,
              path: r.path,
              relevance: r.score,
              excerpt: r.excerpt,
            })),
          }),
        }],
      };
    }
  );
}
```

#### Step 5: Package Configuration

```json
// package.json
{
  "name": "moose-mcp-server",
  "version": "1.0.0",
  "type": "module",
  "main": "dist/index.js",
  "bin": {
    "moose-mcp": "dist/index.js"
  },
  "scripts": {
    "build": "tsc",
    "dev": "tsx src/index.ts",
    "start": "node dist/index.js"
  }
}
```

---

## End User Setup

### For Cursor Users

Add to `~/.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "moose": {
      "command": "node",
      "args": ["~/.moose-mcp-server/dist/index.js"]
    }
  }
}
```

Or if installed globally:

```json
{
  "mcpServers": {
    "moose": {
      "command": "moose-mcp"
    }
  }
}
```

### For Claude Desktop Users

Add to Claude's MCP configuration (`~/Library/Application Support/Claude/claude_desktop_config.json` on macOS):

```json
{
  "mcpServers": {
    "moose": {
      "command": "node",
      "args": ["/Users/username/.moose-mcp-server/dist/index.js"]
    }
  }
}
```

### For Claude Code Users

Claude Code supports MCP servers. Add to your workspace or user MCP configuration following Claude Code's documentation.

### Hosted Server Setup

If using a hosted server (Option C), configure the URL:

```json
{
  "mcpServers": {
    "moose": {
      "url": "https://moose-mcp.your-org.com/sse",
      "transport": "sse"
    }
  }
}
```

---

## Developer Onboarding

### One-Time Setup

```bash
# 1. Clone MCP server repository
git clone git@github.com:your-org/moose-mcp-server.git ~/.moose-mcp-server

# 2. Install dependencies and build
cd ~/.moose-mcp-server
npm install
npm run build

# 3. Ensure knowledge base is cloned (from main KB plan)
git clone git@github.com:your-org/moose-knowledge.git ~/.moose-knowledge

# 4. Add MCP configuration to Cursor
# (Add the JSON config shown above to ~/.cursor/mcp.json)

# 5. Restart Cursor to pick up the new MCP server
```

### Updating

```bash
# Update both knowledge base and MCP server
cd ~/.moose-knowledge && git pull
cd ~/.moose-mcp-server && git pull && npm run build
```

Or create an alias:

```bash
alias moose-update='(cd ~/.moose-knowledge && git pull) && (cd ~/.moose-mcp-server && git pull && npm run build)'
```

---

## Maintenance

### Regular Tasks

| Task | Frequency | Owner |
|------|-----------|-------|
| Update local knowledge base | Weekly | Each developer |
| Review add_learning PRs | Weekly | Tech lead |
| Update MCP server code | As needed | Maintainer |
| Monitor search quality | Monthly | Team |

### Updating the Server

When MCP server code changes:

```bash
cd ~/.moose-mcp-server
git pull
npm install
npm run build
# Restart Cursor or Claude Desktop
```

### Adding New Tools

1. Create new tool file in `src/tools/`
2. Register tool in `src/tools/index.ts`
3. Build and test locally
4. Commit and push
5. Notify team to update

---

## Future Enhancements

### Semantic Search

Add embeddings-based search for better relevance:

1. Index all documents with OpenAI embeddings
2. Store in vector database (Pinecone, ChromaDB)
3. Query by semantic similarity

**Benefits:**
- Finds relevant patterns even without exact keyword matches
- "Show me blocks similar to the carousel" works
- Better handling of synonyms and related concepts

### Usage Analytics

Track which tools and patterns are most used:

```typescript
// Log each tool invocation
server.on("toolCall", (tool, params) => {
  analytics.track("mcp_tool_call", {
    tool: tool.name,
    params,
    timestamp: new Date().toISOString(),
  });
});
```

**Benefits:**
- Understand what developers search for most
- Identify gaps in documentation
- Prioritize which patterns to add

### Auto-Suggestions

Proactively suggest relevant patterns:

```typescript
{
  name: "suggest_patterns",
  description: "Get AI-powered suggestions for patterns that might be relevant to current work",
  inputSchema: {
    type: "object",
    properties: {
      context: {
        type: "string",
        description: "Description of what you're currently working on"
      }
    }
  }
}
```

---

## Implementation Timeline

### Week 1: Foundation
- [ ] Create `moose-mcp-server` repository
- [ ] Set up TypeScript project structure
- [ ] Implement knowledge loader and search index
- [ ] Implement `search_patterns` and `get_pattern` tools

### Week 2: Core Tools
- [ ] Implement `list_patterns` tool
- [ ] Implement `get_solution` tool
- [ ] Implement `get_checklist` tool
- [ ] Local testing with Cursor

### Week 3: Contributions
- [ ] Implement `add_learning` tool with GitHub PR creation
- [ ] Test full workflow
- [ ] Write developer documentation

### Week 4: Rollout
- [ ] Developer onboarding documentation
- [ ] Team setup and testing
- [ ] Gather feedback and iterate

### Future
- [ ] Add semantic search (embeddings)
- [ ] Usage analytics
- [ ] Consider hosted option if team grows

---

## Appendix: Complete Tool Reference

| Tool | Purpose | Required Params |
|------|---------|-----------------|
| `moose:search_patterns` | Search knowledge base | `query` |
| `moose:get_pattern` | Get full document content | `path` |
| `moose:list_patterns` | List patterns in category | `category` |
| `moose:get_solution` | Find solution for problem | `problem` |
| `moose:add_learning` | Submit new knowledge (PR) | `type`, `title`, `content` |
| `moose:get_checklist` | Get task checklist | `task` |
