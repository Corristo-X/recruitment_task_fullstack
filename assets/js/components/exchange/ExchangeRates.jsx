import React, {useState, useEffect} from 'react';
import {useParams, useHistory} from 'react-router-dom';
import axios from 'axios';
import ExchangeRateTable from "./ExchangeRateTable";

function ExchangeRates() {
  const {date} = useParams();
  const history = useHistory();
  const today = new Date().toISOString().split('T')[0];
  const [selectedDate, setSelectedDate] = useState(date || today);
  const [exchangedData, setExchangedData] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!date) {
      history.replace(`/exchange-rates/${today}`);
    } else {
      setSelectedDate(date);
      setLoading(true);
      axios.get(`/api/exchange-rates/${date}`)
        .then(response => {
          setExchangedData(response.data);
          setLoading(false);
        })
        .catch(err => {
          console.error('API call failed:', err);
        });
    }
  }, [date, history, today]);

  const handleDateChange = (event) => {
    const newDate = event.target.value;
    setSelectedDate(newDate);
    history.push(`/exchange-rates/${newDate}`);
  };

  return (
    <>
      <h1>Exchange Rates</h1>
      <input type="date" value={selectedDate} onChange={handleDateChange} min="2023-01-01" max={today}/>
      {loading ? (
        <div className={'text-center'}>
          <span className="fa fa-spin fa-spinner fa-4x"></span>
        </div>
      ) : (
        exchangedData && <ExchangeRateTable exchangeData={exchangedData}/>
      )}
    </>
  );
}

export default ExchangeRates;
