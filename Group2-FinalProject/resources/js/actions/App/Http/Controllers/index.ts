import TicketController from './TicketController'
import CommentController from './CommentController'
import AdminUserController from './AdminUserController'
import Settings from './Settings'

const Controllers = {
    TicketController: Object.assign(TicketController, TicketController),
    CommentController: Object.assign(CommentController, CommentController),
    AdminUserController: Object.assign(AdminUserController, AdminUserController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers