// @flow
import * as React from 'react';
import { HelpBlock } from 'react-bootstrap';
import Select from 'react-select';
import Async from 'react-select/lib/Async';
import { FormattedMessage } from 'react-intl';

type Options = Array<{ value: string, label: string }>;
type Value = string | Array<{ value: string }>;
type OnChangeInput = { value: string } | Array<{ value: string }>;
type Props = {
  input: {
    name: string,
    value: Value,
    onBlur: () => void,
    onChange: (value: Value) => void,
    onFocus: () => void,
  },
  id: string,
  meta: { touched: boolean, error: ?string },
  label: string | React.Node,
  help: string | React.Node,
  placeholder?: string,
  autoload?: boolean,
  clearable?: boolean,
  disabled?: boolean,
  multi: boolean,
  options?: Options, // or loadOptions for async
  loadOptions?: () => Options, // or options for sync
  filterOption?: Function,
  onChange: () => void,
  labelClassName?: string,
  inputClassName?: string,
};

const ClearIndicator = props => {
  const {
    innerProps: { ref, ...restInnerProps },
  } = props;
  return (
    <div role="button" className="select__clear-zone" {...restInnerProps} ref={ref}>
      <i className="cap cap-times mr-10 ml-10" />
    </div>
  );
};

class renderSelect extends React.Component<Props> {
  static defaultProps = {
    multi: false,
    disabled: false,
    autoload: false,
    clearable: true,
  };

  render() {
    const {
      onChange,
      input,
      label,
      labelClassName,
      inputClassName,
      multi,
      options,
      disabled,
      autoload,
      clearable,
      placeholder,
      loadOptions,
      filterOption,
      id,
      help,
      meta: { touched, error },
    } = this.props;
    const { name, value, onBlur, onFocus } = input;

    let selectValue = null;
    let selectLabel = null;

    if (typeof loadOptions === 'function') {

    } else {
      if (multi) {
        console.log(options);
        selectLabel =
          options && options.filter(option => option && option.value && option.value === value);
        selectValue = value ? selectLabel && selectLabel : [];
        console.log(selectValue);
      } else {
        selectLabel =
          options && options.filter(option => option && option.value && option.value === value);
        selectValue = value ? selectLabel && selectLabel[0] : null;
      }
    }

    return (
      <div className="form-group">
        {label && (
          <label htmlFor={id} className={labelClassName || 'control-label'}>
            {label}
          </label>
        )}
        {help && <HelpBlock>{help}</HelpBlock>}
        <div id={id} className={inputClassName || ''}>
          {typeof loadOptions === 'function' ? (
            <Async
              filterOption={filterOption}
              ref={ref}
              components={{ ClearIndicator }}
              isDisabled={disabled}
              defaultOptions={autoload}
              isClearable={clearable}
              placeholder={
                placeholder || <FormattedMessage id="admin.fields.menu_item.parent_empty" />
              }
              loadOptions={loadOptions}
              // onBlurResetsInput={false}
              cacheOptions
              // onCloseResetsInput={false}
              value={selectValue}
              name={name}
              isMulti={multi}
              noOptionsMessage={() => <FormattedMessage id="select.no-results" />}
              loadingMessage={() => <FormattedMessage id="global.loading" />}
              onBlur={() => onBlur()}
              onFocus={onFocus}
              onChange={(newValue: OnChangeInput) => {
                if (typeof onChange === 'function') {
                  onChange();
                }
                if (multi && Array.isArray(newValue)) {
                  input.onChange(newValue);
                  return;
                }
                if (!Array.isArray(newValue)) {
                  input.onChange(newValue ? newValue.value : '');
                }
              }}
            />
          ) : (
            <Select
              name={name}
              components={{ ClearIndicator }}
              isDisabled={disabled}
              options={options}
              filterOption={filterOption}
              onBlurResetsInput={false}
              onCloseResetsInput={false}
              placeholder={
                placeholder || <FormattedMessage id="admin.fields.menu_item.parent_empty" />
              }
              isClearable={clearable}
              isMulti={multi}
              value={selectValue}
              noOptionsMessage={() => <FormattedMessage id="select.no-results" />}
              loadingMessage={() => <FormattedMessage id="global.loading" />}
              onBlur={() => onBlur()}
              onFocus={onFocus}
              onChange={(newValue: OnChangeInput) => {
                if (typeof onChange === 'function') {
                  onChange();
                }
                if (multi && Array.isArray(newValue)) {
                  return input.onChange(newValue);
                }
                if (!Array.isArray(newValue)) {
                  input.onChange(newValue ? newValue.value : '');
                }
              }}
            />
          )}
          {touched && error}
        </div>
      </div>
    );
  }
}

export default renderSelect;
