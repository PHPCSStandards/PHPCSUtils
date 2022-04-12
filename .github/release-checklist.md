Template to use for release PRs from `develop` to `master`
===========================================================

PR for tracking changes for the x.x.x release. Target release date: **DOW MONTH DAY YEAR**.

## Release checklist

### Update website
- [ ] Regenerate the PHPDoc documentation - PR #xxx
    :pencil2: Clear out the `docs/phpdoc` directory and then use phpDocumentor 3 with the command `phpdoc`
- [ ] Sync any changes in the Readme into the website `index.md` file. - PR #xxx
    :pencil2: Copy & paste the content from the `README.md` file to `docs/index.md` (and double-check the few remaining differences are intact).
    To verify the output locally:
    ```bash
    bundle update
    bundle exec jekyll serve
    ```
    and then visiting http://localhost:4000/ to see the result.

### General
- [ ] Update the `DEVMASTER` version nr constant in the `Tests/BackCompat/Helper/GetVersionTest.php` file. - PR #xxx
- [ ] Verify, and if necessary, update the version constraints for dependencies in the `composer.json` - PR #xxx
- [ ] Verify that any new functions have type declarations whenever possible.
- [ ] Add changelog for the release - PR #xxx
    :pencil2: Remember to add a release link at the bottom and to adjust the link for "Unreleased"!

### Release
- [ ] Merge this PR
- [ ] Make sure all CI builds are green.
- [ ] Verify that the website regenerated correctly.
- [ ] Tag the release (careful, GH defaults to `develop`!).
- [ ] Create a release from the tag (careful, GH defaults to `develop`!) & copy & paste the changelog to it.
    :pencil2: Don't forget to copy the link collection from the bottom of the changelog!
- [ ] Close the milestone
- [ ] Open a new milestone for the next release
- [ ] If any open PRs/issues which were milestoned for this release did not make it into the release, update their milestone.
- [ ] Fast-forward `develop` to be equal to `master`

### Publicize
- [ ] Tweet about the release.
- [ ] Inform the primary dependants of this repo (PHPCSExtra, WordPressCS, PHPCompatibility and VariableAnalysis) about the release.
