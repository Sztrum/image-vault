import type { AnchorHTMLAttributes } from 'react'

import { buttonClassName, type ButtonVariant } from './buttonClassName'

type ButtonLinkProps = AnchorHTMLAttributes<HTMLAnchorElement> & {
  variant?: ButtonVariant
}

export function ButtonLink({ variant = 'primary', className = '', ...props }: ButtonLinkProps) {
  return <a className={buttonClassName(variant, className)} {...props} />
}
