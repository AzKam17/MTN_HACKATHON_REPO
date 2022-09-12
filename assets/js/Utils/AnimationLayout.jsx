import { Outlet, useLocation } from 'react-router-dom'
import { motion } from "framer-motion"

const AnimationLayout = () => {
  const { pathname } = useLocation();
  return (
    <motion.div
      key={pathname}
      initial="initial"
      animate="in"
      variants={pageVariants}
      transition={pageTransition}
    >
      <Outlet />
    </motion.div>
  );
};