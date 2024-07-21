import React from 'react';
import PropTypes from 'prop-types';
import exchangeDataPropTypes from "./exchangeDataPropTypes";

function ExchangeRateTable({exchangeData}) {
  const noRatesAvailable = Object.keys(exchangeData?.rates ?? {}).length === 0 && Object.keys(exchangeData?.current_rates ?? {}).length === 0;
  const ratesToMap = Object.keys(exchangeData?.rates ?? {}).length ? exchangeData.rates : exchangeData?.current_rates ?? {};
  return (
    <>
      {noRatesAvailable ? (
        <p className="no-data-message">No data available for the provided date.</p>
      ) : (
        <>
          <table>
            <thead>
            <tr>
              <th>Code + Currency</th>
              <th>NBP Rate (Selected Date)</th>
              <th>Buy Rate (Selected Date)</th>
              <th>Sell Rate (Selected Date)</th>
              <th>NBP Rate (Today)</th>
              <th>Buy Rate (Today)</th>
              <th>Sell Rate (Today)</th>
            </tr>
            </thead>
            <tbody>
            {Object.keys(ratesToMap).map(code => (
              <tr key={code}>
                <td>{code + ' - ' + (exchangeData.rates[code]?.currency || exchangeData.current_rates[code]?.currency || 'N/A')}</td>
                <td
                  className="important">{exchangeData.rates[code]?.nbp ? exchangeData.rates[code].nbp.toFixed(4) : 'N/A'}</td>
                <td className="important">
                  {exchangeData.rates[code]?.buy
                    ? exchangeData.rates[code].buy.toFixed(4)
                    : (code !== 'EUR' && code !== 'USD' ? '-' : 'N/A')}
                </td>
                <td
                  className="important">{exchangeData.rates[code]?.sell ? exchangeData.rates[code].sell.toFixed(4) : 'N/A'}</td>
                <td>{exchangeData.current_rates[code]?.nbp ? exchangeData.current_rates[code].nbp.toFixed(4) : 'N/A'}</td>
                <td>
                  {exchangeData.current_rates[code]?.buy
                    ? exchangeData.current_rates[code].buy.toFixed(4)
                    : (code !== 'EUR' && code !== 'USD' ? '-' : 'N/A')}
                </td>
                <td>{exchangeData.current_rates[code]?.sell ? exchangeData.current_rates[code].sell.toFixed(4) : 'N/A'}</td>
              </tr>
            ))}
            </tbody>
          </table>
          <p><strong>Note:</strong> 'N/A' indicates that data is not available for the specified date.</p>
          <p><strong>Note:</strong> '-' indicates that the exchange does not buy the specified currency.</p>
        </>
      )}
    </>
  )
}

ExchangeRateTable.propTypes = {
  exchangeData: PropTypes.shape(exchangeDataPropTypes).isRequired,
};

export default ExchangeRateTable;
