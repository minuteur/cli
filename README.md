# Minuteur CLI

CLI tool to integrate with the Minuteur API.

To download the latest version, go to [releases](https://github.com/minuteur/cli/releases/latest) and download the latest version.

## Commands

**Start a timer for a project**

```bash
minuteur timer:start "ced06672-0da1-43a7-aba4-d9ad2ab817ab"
```

**Stop the timer for a project**

```bash
minuteur timer:stop "ced06672-0da1-43a7-aba4-d9ad2ab817ab" "Session name"
```

**List projects for an Alfred Workflow**

```bash
minuteur alfred:projects:fetch [--query="Project name"] [--only-running]
```

**Publish the hours to freshbooks**

```bash
minuteur freshbooks:publish
```

For this, make sure you have the following variables exported in your bash (usually putting in a place like `~/.bash_profile` does the job):

```bash
export FRESHBOOKS_SUB_DOMAIN=
export `FRESHBOOKS_API_TOKEN=
```

Note: If you are using the Alfred workflow, you will have to set this up as environment variables in the Alfred Workflow as well.
