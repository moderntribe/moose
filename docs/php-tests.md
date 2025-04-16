# PHP Tests

A test suite is ready to use utilizing [Slic](https://github.com/stellarwp/slic). You can follow the instructions on the Slic readme to configure
testing locally. Slic utilizes [WP-Browser](https://wpbrowser.wptestkit.dev/) and [Codeception](https://codeception.com/) to run tests in a docker container allowing us
to use all the generate commands those libraries have to offer.

The only major setup config you must do for slic is set the php-version to the correct version for this project.
You can do this by running `slic php-version set <version>`.

Once Slic is installed, you can go to the project root and enter `slic here` telling slic that you want to run tests
from this folder.  Then run `slic use site` telling slic that you want to run the tests for the full site and not just
a singular plugin or theme. Then you are ready to start testing by running `slic run wpunit`. You can exchange out the
`wpunit` for any of the testing suites you would like to run (`wpunit`, `unit`, `functional`, or `acceptance`).
