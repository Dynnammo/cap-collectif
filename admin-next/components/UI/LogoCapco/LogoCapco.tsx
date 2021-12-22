import type { FC } from 'react';
import { Box, BoxPropsOf } from '@cap-collectif/ui';

interface LogoCapcoProps extends BoxPropsOf<'svg'> {}

const LogoCapco: FC<LogoCapcoProps> = ({ width, height }) => (
    <Box
        as="svg"
        width={width}
        height={height}
        viewBox="0 0 124 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M38.3672 8.01562C37.2943 8.01562 36.4505 8.39583 35.8359 9.15625C35.2214 9.91667 34.9141 10.9661 34.9141 12.3047C34.9141 13.7057 35.2083 14.7656 35.7969 15.4844C36.3906 16.2031 37.2474 16.5625 38.3672 16.5625C38.8516 16.5625 39.3203 16.5156 39.7734 16.4219C40.2266 16.3229 40.6979 16.1979 41.1875 16.0469V17.6484C40.2917 17.987 39.276 18.1562 38.1406 18.1562C36.4688 18.1562 35.1849 17.651 34.2891 16.6406C33.3932 15.625 32.9453 14.1745 32.9453 12.2891C32.9453 11.1016 33.1615 10.0625 33.5938 9.17188C34.0312 8.28125 34.6615 7.59896 35.4844 7.125C36.3073 6.65104 37.2734 6.41406 38.3828 6.41406C39.5495 6.41406 40.6276 6.65885 41.6172 7.14844L40.9453 8.70312C40.5599 8.52083 40.151 8.36198 39.7188 8.22656C39.2917 8.08594 38.8411 8.01562 38.3672 8.01562ZM48.8594 18L48.4922 16.7969H48.4297C48.013 17.3229 47.5938 17.6823 47.1719 17.875C46.75 18.0625 46.2083 18.1562 45.5469 18.1562C44.6979 18.1562 44.0339 17.9271 43.5547 17.4688C43.0807 17.0104 42.8438 16.362 42.8438 15.5234C42.8438 14.6328 43.1745 13.9609 43.8359 13.5078C44.4974 13.0547 45.5052 12.8073 46.8594 12.7656L48.3516 12.7188V12.2578C48.3516 11.7057 48.2214 11.2943 47.9609 11.0234C47.7057 10.7474 47.3073 10.6094 46.7656 10.6094C46.3229 10.6094 45.8984 10.6745 45.4922 10.8047C45.0859 10.9349 44.6953 11.0885 44.3203 11.2656L43.7266 9.95312C44.1953 9.70833 44.7083 9.52344 45.2656 9.39844C45.8229 9.26823 46.349 9.20312 46.8438 9.20312C47.9427 9.20312 48.7708 9.44271 49.3281 9.92188C49.8906 10.401 50.1719 11.1536 50.1719 12.1797V18H48.8594ZM46.125 16.75C46.7917 16.75 47.3255 16.5651 47.7266 16.1953C48.1328 15.8203 48.3359 15.2969 48.3359 14.625V13.875L47.2266 13.9219C46.362 13.9531 45.7318 14.099 45.3359 14.3594C44.9453 14.6146 44.75 15.0078 44.75 15.5391C44.75 15.9245 44.8646 16.224 45.0938 16.4375C45.3229 16.6458 45.6667 16.75 46.125 16.75ZM57.1172 18.1562C56.0234 18.1562 55.1745 17.763 54.5703 16.9766H54.4609C54.5339 17.7057 54.5703 18.1484 54.5703 18.3047V21.8438H52.7344V9.35938H54.2188C54.2604 9.52083 54.3464 9.90625 54.4766 10.5156H54.5703C55.1432 9.64062 56.0026 9.20312 57.1484 9.20312C58.2266 9.20312 59.0651 9.59375 59.6641 10.375C60.2682 11.1562 60.5703 12.2526 60.5703 13.6641C60.5703 15.0755 60.263 16.1771 59.6484 16.9688C59.0391 17.7604 58.1953 18.1562 57.1172 18.1562ZM56.6719 10.7031C55.9427 10.7031 55.4089 10.9167 55.0703 11.3438C54.737 11.7708 54.5703 12.4531 54.5703 13.3906V13.6641C54.5703 14.7161 54.737 15.4792 55.0703 15.9531C55.4036 16.4219 55.9479 16.6562 56.7031 16.6562C57.3385 16.6562 57.8281 16.3958 58.1719 15.875C58.5156 15.3542 58.6875 14.612 58.6875 13.6484C58.6875 12.6797 58.5156 11.9479 58.1719 11.4531C57.8333 10.9531 57.3333 10.7031 56.6719 10.7031ZM72.0391 7.13281C70.6068 7.13281 69.4792 7.59115 68.6562 8.50781C67.8333 9.42448 67.4219 10.6797 67.4219 12.2734C67.4219 13.9036 67.8099 15.1693 68.5859 16.0703C69.362 16.9714 70.4714 17.4219 71.9141 17.4219C72.8724 17.4219 73.7526 17.2995 74.5547 17.0547V17.7578C73.7995 18.0234 72.8568 18.1562 71.7266 18.1562C70.1224 18.1562 68.8594 17.638 67.9375 16.6016C67.0156 15.5651 66.5547 14.1172 66.5547 12.2578C66.5547 11.0964 66.7734 10.0729 67.2109 9.1875C67.6536 8.30208 68.2865 7.61979 69.1094 7.14062C69.9375 6.65625 70.8984 6.41406 71.9922 6.41406C73.1068 6.41406 74.1042 6.6224 74.9844 7.03906L74.6641 7.75781C73.8307 7.34115 72.9557 7.13281 72.0391 7.13281ZM83.9297 13.7422C83.9297 15.1276 83.5938 16.2109 82.9219 16.9922C82.25 17.7682 81.3229 18.1562 80.1406 18.1562C79.3958 18.1562 78.7396 17.9766 78.1719 17.6172C77.6042 17.2578 77.1693 16.7422 76.8672 16.0703C76.5651 15.3984 76.4141 14.6224 76.4141 13.7422C76.4141 12.3568 76.75 11.2786 77.4219 10.5078C78.0938 9.73177 79.0156 9.34375 80.1875 9.34375C81.3542 9.34375 82.2682 9.73698 82.9297 10.5234C83.5964 11.3047 83.9297 12.3776 83.9297 13.7422ZM77.2266 13.7422C77.2266 14.9089 77.4818 15.8203 77.9922 16.4766C78.5078 17.1276 79.2344 17.4531 80.1719 17.4531C81.1094 17.4531 81.8333 17.1276 82.3438 16.4766C82.8594 15.8203 83.1172 14.9089 83.1172 13.7422C83.1172 12.5703 82.8568 11.6615 82.3359 11.0156C81.8203 10.3698 81.0938 10.0469 80.1562 10.0469C79.2188 10.0469 78.4948 10.3698 77.9844 11.0156C77.4792 11.6562 77.2266 12.5651 77.2266 13.7422ZM87.0547 18H86.2812V5.84375H87.0547V18ZM90.6797 18H89.9062V5.84375H90.6797V18ZM96.9609 18.1562C95.7266 18.1562 94.763 17.776 94.0703 17.0156C93.3828 16.2552 93.0391 15.1901 93.0391 13.8203C93.0391 12.4661 93.3724 11.3828 94.0391 10.5703C94.7057 9.7526 95.6042 9.34375 96.7344 9.34375C97.7344 9.34375 98.5234 9.69271 99.1016 10.3906C99.6797 11.0885 99.9688 12.0365 99.9688 13.2344V13.8594H93.8516C93.862 15.026 94.1328 15.9167 94.6641 16.5312C95.2005 17.1458 95.9661 17.4531 96.9609 17.4531C97.4453 17.4531 97.8698 17.4193 98.2344 17.3516C98.6042 17.2839 99.0703 17.138 99.6328 16.9141V17.6172C99.1536 17.8255 98.7109 17.9661 98.3047 18.0391C97.8984 18.1172 97.4505 18.1562 96.9609 18.1562ZM96.7344 10.0312C95.9167 10.0312 95.2604 10.3021 94.7656 10.8438C94.2708 11.3802 93.9818 12.1562 93.8984 13.1719H99.1484C99.1484 12.1875 98.9349 11.4193 98.5078 10.8672C98.0807 10.3099 97.4896 10.0312 96.7344 10.0312ZM105.703 18.1562C104.49 18.1562 103.539 17.7734 102.852 17.0078C102.164 16.2422 101.82 15.1745 101.82 13.8047C101.82 12.3984 102.177 11.3047 102.891 10.5234C103.604 9.73698 104.581 9.34375 105.82 9.34375C106.555 9.34375 107.258 9.47135 107.93 9.72656L107.719 10.4141C106.984 10.1693 106.346 10.0469 105.805 10.0469C104.763 10.0469 103.974 10.3698 103.438 11.0156C102.901 11.6562 102.633 12.5807 102.633 13.7891C102.633 14.9349 102.901 15.8333 103.438 16.4844C103.974 17.1302 104.724 17.4531 105.688 17.4531C106.458 17.4531 107.174 17.3151 107.836 17.0391V17.7578C107.294 18.0234 106.583 18.1562 105.703 18.1562ZM112.164 17.4688C112.654 17.4688 113.081 17.4271 113.445 17.3438V17.9688C113.07 18.0938 112.638 18.1562 112.148 18.1562C111.398 18.1562 110.844 17.9557 110.484 17.5547C110.13 17.1536 109.953 16.5234 109.953 15.6641V10.1797H108.695V9.72656L109.953 9.375L110.344 7.45312H110.742V9.50781H113.234V10.1797H110.742V15.5547C110.742 16.2057 110.857 16.6875 111.086 17C111.315 17.3125 111.674 17.4688 112.164 17.4688ZM116.055 18H115.281V9.50781H116.055V18ZM115.172 7.14844C115.172 6.64844 115.336 6.39844 115.664 6.39844C115.826 6.39844 115.951 6.46354 116.039 6.59375C116.133 6.72396 116.18 6.90885 116.18 7.14844C116.18 7.38281 116.133 7.56771 116.039 7.70312C115.951 7.83854 115.826 7.90625 115.664 7.90625C115.336 7.90625 115.172 7.65365 115.172 7.14844ZM122.062 10.1797H120.062V18H119.289V10.1797H117.711V9.72656L119.289 9.4375V8.78125C119.289 7.73958 119.479 6.97656 119.859 6.49219C120.245 6.0026 120.872 5.75781 121.742 5.75781C122.211 5.75781 122.68 5.82812 123.148 5.96875L122.969 6.64062C122.552 6.51042 122.138 6.44531 121.727 6.44531C121.122 6.44531 120.693 6.625 120.438 6.98438C120.188 7.33854 120.062 7.91667 120.062 8.71875V9.50781H122.062V10.1797Z"
            fill="white"
        />
        <path
            d="M15.4839 8.58065L9.67742 11.871L3.87097 15.1613V8.58065V2L9.67742 5.29032L15.4839 8.58065Z"
            fill="white"
        />
        <path
            d="M13.9355 11.6766L6.96774 15.7424L0 19.8065V11.6766V3.5484L6.96774 7.61248L13.9355 11.6766Z"
            fill="#FFAC00"
        />
        <path
            d="M24 12.0645L15.4839 16.7097L6.96774 21.3548V12.0645V2.77417L15.4839 7.41933L24 12.0645Z"
            fill="#38E5A3"
        />
        <g style={{ mixBlendMode: 'multiply' }}>
            <path
                d="M15.4839 8.58065L9.67742 11.871L3.87097 15.1613V8.58065V2L9.67742 5.29032L15.4839 8.58065Z"
                fill="#FF176D"
            />
        </g>
    </Box>
);

export default LogoCapco;
