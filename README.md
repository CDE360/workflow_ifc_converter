# Nextcloud IFC Converter app

This app lets Nextcloud automatically convert model files to xkt for visualization. By utilizing the workflow engine it allows Nextcloud administrators to define rules upon which various documents are enqueued to be converted to xkt. Eventually, the conversion happens in a background job by feeding the source file to the found or specific conversion script. Depending on the selected behaviour the source file can either be kept or deleted and the resulting files can either be preserved by increasing a number added to the filename or overwritten.

The conversion job is being created when a file was created or updated and also when a system tag was assigned.

Learn more about workflows on https://nextcloud.com/workflow

## Requirements

IFC Files conversion.

## Limitations

This app does not work with either encryption method.

