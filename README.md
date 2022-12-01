# hog-4-pco-import

![ScreenShot](https://github.com/matt-flaig/hog-4-pco-import/blob/master/screenshot.png?raw=true)
This tool uses the Planning Center Services API to convert a Services rundown into an ETC Hog 4 console cuelist (by generating an XML file that can be imported into the console).

## Getting Started
Download this repo.

Add your `PCO_API_KEY` and `PCO_API_SECRET` to **generateXML.php**.

Add your `PCO_SERVICE_TYPE` to **generateXML.php**. (It's typically the multi-digit number after the `https://services.planningcenteronline.com/dashboard/` url).

Upload the files to a web server.

Load index.html.

Press "Generate" to create and download the XML file.
