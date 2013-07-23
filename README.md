BART PHP SDK
================

Provides a PHP SDK for the [BART API](http://www.bart.gov/schedules/developers/api.aspx).

## Usage

### Instantiating a connection object.

Before making requests to BART API, you need to instatiate a bart object, which
will verify your API key and return the BART API version.

    require_once("bart.php");
    $key = 'yourAPIkey';
    $bart = new Bart($key);
    print 'Using BART API version ' . $bart->version;

### Real Time Estimates
    // All stations.
    $rte_data = $bart->getRealTimeEstimate();

    // Richmond station.
    $rte_data = $bart->getRealTimeEstimate('rich');
