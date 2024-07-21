import PropTypes from 'prop-types';

const ratePropType = PropTypes.shape({
  currency: PropTypes.string.isRequired,
  nbp: PropTypes.number.isRequired,
  buy: PropTypes.number,
  sell: PropTypes.number.isRequired,
});

const ratesPropType = PropTypes.oneOfType([
  PropTypes.objectOf(ratePropType),
  PropTypes.arrayOf(ratePropType)
]);

const exchangeDataPropTypes = {
  date: PropTypes.string.isRequired,
  rates: ratesPropType.isRequired,
  current_rates: ratesPropType.isRequired,
};

export default exchangeDataPropTypes;
