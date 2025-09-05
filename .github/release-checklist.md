# Template to use for release PRs from `develop` to `stable`

PR for tracking changes for the x.x.x release. Target release date: **DOW MONTH DAY YEAR**.

## Release checklist

### General

- [ ] Verify, and if necessary, update the version constraints for dependencies in the `composer.json` - PR #xxx
- [ ] Verify that any new functions have type declarations whenever possible.
- [ ] Add changelog for the release - PR #xxx
    :pencil2: Remember to add a release link at the bottom!

### Release

- [ ] Merge this PR
- [ ] Make sure all CI builds are green.
- [ ] Tag and create a release (careful, GH defaults to `develop`!) & copy & paste the changelog to it.
    :pencil2: Don't forget to copy the link collection from the bottom of the changelog!
- [ ] Make sure all CI builds are green.
- [ ] Verify that the website regenerated correctly.
- [ ] Close the milestone
- [ ] Open a new milestone for the next release
- [ ] If any open PRs/issues which were milestoned for this release did not make it into the release, update their milestone.
- [ ] Fast-forward `develop` to be equal to `stable`

### Publicize

- [ ] Post on Mastodon about the release.
- [ ] Tweet about the release.
- [ ] Inform the primary dependants of this repo (PHPCSExtra, WordPressCS, PHPCompatibility and VariableAnalysis) about the release.
